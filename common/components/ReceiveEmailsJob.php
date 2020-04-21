<?php

namespace common\components;


use common\components\jobs\CreateSaleFromBOJob;
use common\models\DepartmentEmailProject;
use common\models\DepartmentPhoneProject;
use common\models\Lead;
use frontend\widgets\notification\NotificationMessage;
use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\helpers\app\AppHelper;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesCommunicationService;
use sales\services\cases\CasesManageService;
use sales\services\cases\CasesSaleService;
use sales\services\client\ClientManageService;
use sales\services\email\EmailService;
use sales\services\email\incoming\EmailIncomingService;
use sales\services\internalContact\InternalContactService;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use Yii;
use common\models\Email;
use common\models\Notifications;
use common\components\CommunicationService;
use common\models\Project;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class ReceiveEmailsJob
 * @package common\components
 *
 * @property EmailService
 * @property CasesSaleService $casesSaleService
 */
class ReceiveEmailsJob extends BaseObject implements \yii\queue\JobInterface
{
	/**
	 * @var EmailService
	 */
	private $emailService;
	private $casesSaleService;

	public $last_email_id = 0;

    public $request_data = [];

    /**
     * @param \yii\queue\Queue $queue
     * @return bool
     * @throws \yii\httpclient\Exception
     */
    public function execute($queue): bool
    {
        $debug = true;
        $filter = [];

        $accessEmailRequest = true;
        $cicleCount = 1;
        $countTotal = 0;

        //Yii::info(VarDumper::dumpAsString(['last_email_id' => $this->last_email_id, 'request_data' => $this->request_data]), 'info\JOB:ReceiveEmailsJob');

        try {
        	$this->emailService = Yii::createObject(EmailService::class);
        	$this->casesSaleService = Yii::createObject(CasesSaleService::class);

            if ((int)$this->last_email_id < 1) {
                \Yii::error('Not found last_email_id (' . $this->last_email_id . ')', 'ReceiveEmailsJob:execute');
                return true;
            }

            if (!count($this->request_data)) {
                \Yii::error('Error request_data  (' . print_r($this->request_data, true) . ')', 'ReceiveEmailsJob:execute');
                return true;
            }

            $filter['last_id'] = (int)$this->last_email_id;

            if (isset($this->request_data['limit'])) {
                $filter['limit'] = (int)$this->request_data['limit'];
            } else {
                $filter['limit'] = 20;
            }

            if (isset($this->request_data['email_list'])) {
                $filter['email_list'] = $this->request_data['email_list'];
            } else {
                $filter['email_list'] = [];
            }

            /** @var CommunicationService $communication */
            $communication = Yii::$app->communication;

            $leadArray = [];
            $caseArray = [];
            $userArray = [];

            while ($accessEmailRequest && $cicleCount < 100) {

                if ($debug) {
                    echo "Cicle #" . $cicleCount . PHP_EOL;
                }

                $res = $communication->mailGetMessages($filter);

                if (isset($res['error']) && $res['error']) {
                    $response['error'] = 'Error mailGetMessages';
                    $response['error_code'] = 13;
                    \Yii::error(VarDumper::dumpAsString($res['error']), 'ReceiveEmailsJob:execute');
                    $cicleCount--;
                } elseif (
                    isset($res['data']['emails']) &&
                    $res['data']['emails']
                    && \is_array($res['data']['emails'])
                    && isset($res['data']['emails'][0])
                    && $res['data']['emails'][0]
                ) {

                    foreach ($res['data']['emails'] as $mail) {

                        $filter['last_id'] = $mail['ei_id'] + 1;

                        $find = Email::find()->where([
                                "e_message_id" => $mail['ei_message_id'],
                                "e_email_to" => $mail['ei_email_to']]
                        )->one();

                        if ($find) {
                            $find->e_inbox_email_id = $mail['ei_id'];
                            $find->save();
                            continue;
                        }

                        $email = new Email();
                        $email->e_type_id = Email::TYPE_INBOX;
                        $email->e_status_id = Email::STATUS_DONE;
                        $email->e_is_new = true;
                        $email->e_email_to = $mail['ei_email_to'];
                        $email->e_email_to_name = $mail['ei_email_to_name'] ?? null;
                        $email->e_email_from = $mail['ei_email_from'];
                        $email->e_email_from_name = $mail['ei_email_from_name'] ?? null;
                        $email->e_email_subject = $mail['ei_email_subject'];
                        if ($mail['ei_project_id'] > 0) {
                            $project = Project::findOne($mail['ei_project_id']);
                            if ($project) {
                                $email->e_project_id = $project->id;
                            }
                        }
                        $email->body_html = $mail['ei_email_text'];
                        $email->e_created_dt = $mail['ei_created_dt'];

                        $email->e_inbox_email_id = $mail['ei_id'];
                        $email->e_inbox_created_dt = $mail['ei_created_dt'];
                        $email->e_ref_message_id = $mail['ei_ref_mess_ids'];
                        $email->e_message_id = $mail['ei_message_id'];

                        $lead_id = $this->emailService->detectLeadId($email);
                        $case_id = $this->emailService->detectCaseId($email);

                        $users = $email->getUsersIdByEmail();

                        $user_id = 0;
                        if ($users) {
                            foreach ($users as $user_id) {
                                $userArray[$user_id] = $user_id;
                            }
                        }

                        if($user_id > 0) {
                            $email->e_created_user_id = $user_id;
                        }

                        if ($lead_id) {
                            // \Yii::info('Email Detected LeadId ' . $lead_id . ' from ' . $email->e_email_from, 'info\ReceiveEmailsJob:execute');
                            $leadArray[$lead_id] = $lead_id;
                        }

                        if ($case_id) {
							$caseArray[$case_id] = $case_id;
						}

                        try {
                            if (!$email->save()) {
                                \Yii::error(VarDumper::dumpAsString($email->errors), 'ReceiveEmailsJob:execute');
                            } else {
                                if ($lead_id === null && $case_id === null && $this->emailService->isNotInternalEmail($email->e_email_from)) {
                                    try {
                                        $emailIncomingService = Yii::createObject(EmailIncomingService::class);
                                        $process = $emailIncomingService->create(
                                            $email->e_id,
                                            $email->e_email_from,
                                            $email->e_email_to,
                                            $email->e_project_id
                                        );
                                        $email->e_lead_id = $process->leadId;
                                        $email->e_case_id = $process->caseId;
                                        $email->save(false);
                                    } catch (\Throwable $e) {
                                        Yii::error($e->getMessage(), 'ReceiveEmailsJob:EmailIncomingService:create');
                                    }
                                }
                            }
                        } catch (\Throwable $e) {
                            Yii::error(VarDumper::dumpAsString(['attr' => $email->getAttributes(), 'error' => $e->getMessage()]));
                            throw $e;
                        }

//                        if ($email->e_case_id && ($case = Cases::findOne($email->e_case_id))) {
//                            (Yii::createObject(CasesCommunicationService::class))->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_EMAIL);
//                        }
                        if ($email->e_case_id) {
                            (Yii::createObject(CasesManageService::class))->needAction($email->e_case_id);

                            try {
                                $job = new CreateSaleFromBOJob();
                                $job->case_id = $email->e_case_id;
                                $job->email = $email->e_email_from;
                                Yii::$app->queue_job->priority(100)->push($job);
                            } catch (\Throwable $throwable) {
                                Yii::error(AppHelper::throwableFormatter($throwable), 'ReceiveEmailsJob:addToJobFailed');
                            }
                        }

                        $countTotal++;
                    }

                    if (isset($res['data']['pagination'], $res['data']['pagination']['count'])) {
                        if ($res['data']['pagination']['count'] < 1) {
                            break;
                        }
                    }

                } else {
                    $cicleCount--;
                    $accessEmailRequest = false;
                    if ($debug) {
                        echo 'Cicle finish' . PHP_EOL;
                    }
                }
                $cicleCount++;
            }

            if ($userArray) {
                foreach ($userArray as $user_id) {
                    if ($ntf = Notifications::create($user_id, 'New Emails received', 'New Emails received. Check your inbox.', Notifications::TYPE_INFO, true)) {
                        // Notifications::socket($user_id, null, 'getNewNotification', [], true);
                        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                        Notifications::sendSocket('getNewNotification', ['user_id' => $user_id], $dataNotification);
                    }
                }
            }

//            if ($leadArray) {
//                foreach ($leadArray as $lead_id) {
//                    // Notifications::socket(null, $lead_id, 'updateCommunication', [], true);
//                    Notifications::sendSocket('getNewNotification', ['lead_id' => $lead_id]);
//                }
//            }

//            if ($caseArray) {
//                foreach ($caseArray as $case_id) {
//                    // Notifications::socket(null, $lead_id, 'updateCommunication', [], true);
//                    Notifications::sendSocket('getNewNotification', ['case_id' => $case_id]);
//                }
//            }
        } catch (\Throwable $e) {
            \Yii::error($e->getTraceAsString(), 'ReceiveEmailsJob:execute');
        }
        if ($debug) {
            echo "cicleCount:" . $cicleCount . " countTotal:" . $countTotal . PHP_EOL;
        }
        return true;
    }
}
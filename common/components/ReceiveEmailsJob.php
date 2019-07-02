<?php

namespace common\components;


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


class ReceiveEmailsJob extends BaseObject implements \yii\queue\JobInterface
{

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

        try {
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

            if (isset($this->request_data['mail_list'])) {
                $filter['mail_list'] = $this->request_data['mail_list'];
            } else {
                $filter['mail_list'] = [];
            }

            /** @var CommunicationService $communication */
            $communication = Yii::$app->communication;

            $leadArray = [];
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
                } elseif (isset($res['data']['emails']) && $res['data']['emails'] && \is_array($res['data']['emails'])) {

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
                        $email->e_email_body_html = $mail['ei_email_text'];
                        $email->e_created_dt = $mail['ei_created_dt'];

                        $email->e_inbox_email_id = $mail['ei_id'];
                        $email->e_inbox_created_dt = $mail['ei_created_dt'];
                        $email->e_ref_message_id = $mail['ei_ref_mess_ids'];
                        $email->e_message_id = $mail['ei_message_id'];

                        $lead_id = $email->detectLeadId();
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

                        if (!$email->save()) {
                            \Yii::error(VarDumper::dumpAsString($email->errors), 'ReceiveEmailsJob:execute');
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
                    Notifications::create($user_id, 'New Emails received', 'New Emails received. Check your inbox.', Notifications::TYPE_INFO, true);
                    Notifications::socket($user_id, null, 'getNewNotification', [], true);
                }
            }

            if ($leadArray) {
                foreach ($leadArray as $lead_id) {
                    Notifications::socket(null, $lead_id, 'updateCommunication', [], true);
                }
            }
        } catch (\Throwable $e) {
            \Yii::error($e->getTraceAsString(), 'ReceiveEmailsJob:execute');
        }
        if ($debug) {
            echo "cicleCount:" . $cicleCount . " countTotal:" . $countTotal . PHP_EOL;
        }
        return true;
    }
}
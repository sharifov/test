<?php

namespace src\model\email\useCase;

use common\components\jobs\CreateSaleFromBOJob;
use common\components\jobs\WebEngageLeadRequestJob;
use common\components\purifier\Purifier;
use common\models\DepartmentEmailProject;
use common\models\Email;
use common\models\Lead;
use common\models\Notifications;
use common\models\Project;
use common\models\UserProjectParams;
use frontend\widgets\notification\NotificationMessage;
use modules\fileStorage\src\entity\fileCase\FileCase;
use modules\fileStorage\src\entity\fileCase\FileCaseRepository;
use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileClient\FileClientRepository;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\entity\fileLead\FileLeadRepository;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\entity\fileStorage\FileStorageRepository;
use modules\fileStorage\src\FileSystem;
use modules\fileStorage\src\services\CreateByApiDto;
use modules\fileStorage\src\services\url\UrlGenerator;
use modules\webEngage\settings\WebEngageDictionary;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\model\leadData\services\LeadDataCreateService;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use src\services\cases\CasesManageService;
use src\services\cases\CasesSaleService;
use src\services\email\EmailService;
use src\services\email\incoming\EmailIncomingService;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use Yii;

/**
 * Class DownloadMessages
 *
 * @property EmailService $emailService
 * @property CasesSaleService $casesSaleService
 * @property FileSystem $fileSystem
 * @property FileStorageRepository $fileStorageRepository
 * @property FileClientRepository $fileClientRepository
 * @property FileCaseRepository $fileCaseRepository
 * @property FileLeadRepository $fileLeadRepository
 * @property UrlGenerator $urlGenerator
 */
class DownloadEmails
{
    private EmailService $emailService;
    private CasesSaleService $casesSaleService;
    private FileSystem $fileSystem;
    private FileStorageRepository $fileStorageRepository;
    private FileClientRepository $fileClientRepository;
    private FileCaseRepository $fileCaseRepository;
    private FileLeadRepository $fileLeadRepository;
    private UrlGenerator $urlGenerator;

    public function __construct(
        EmailService $emailService,
        CasesSaleService $casesSaleService,
        FileSystem $fileSystem,
        FileStorageRepository $fileStorageRepository,
        FileClientRepository $fileClientRepository,
        FileCaseRepository $fileCaseRepository,
        FileLeadRepository $fileLeadRepository,
        UrlGenerator $urlGenerator
    ) {
        $this->emailService = $emailService;
        $this->casesSaleService = $casesSaleService;
        $this->fileSystem = $fileSystem;
        $this->fileStorageRepository = $fileStorageRepository;
        $this->fileClientRepository = $fileClientRepository;
        $this->fileCaseRepository = $fileCaseRepository;
        $this->fileLeadRepository = $fileLeadRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function download(bool $debug, int $limit): void
    {
        $newMessages = (bool)Yii::$app->redis->get('new_email_message_received');
        if (!$newMessages) {
            return;
        }
        Yii::$app->redis->set('new_email_message_received', false);

        $accessEmailRequest = true;
        $cycleCount = 1;
        $countTotal = 0;

        $lastEmailId = (int)Email::find()->select(['e_inbox_email_id'])->where(['>', 'e_inbox_email_id', 0])->orderBy(['e_inbox_email_id' => SORT_DESC])->scalar();
        if ($lastEmailId === 0) {
            $lastEmailId = 1;
        }

        $filter = [
            'last_id' => $lastEmailId,
            'limit' => $limit,
            'email_list' => Json::encode(['list' => $this->getEmailsForReceivedMessages()])
        ];

        try {
//            $leadArray = [];
//            $caseArray = [];
//            $userArray = [];

            $notifyByCases = [];
            $notifyByLeads = [];

            while ($accessEmailRequest && $cycleCount < 100) {
                if ($debug) {
                    echo "Cycle #" . $cycleCount . PHP_EOL;
                }

                $res = Yii::$app->communication->mailGetMessages($filter);

                if (isset($res['error']) && $res['error']) {
                    $response['error'] = 'Error mailGetMessages';
                    $response['error_code'] = 13;
                    \Yii::error(VarDumper::dumpAsString($res['error']), 'DownloadEmails:getMessages');
                    $cycleCount--;
                } elseif (
                    isset($res['data']['emails']) &&
                    $res['data']['emails'] &&
                    \is_array($res['data']['emails']) &&
                    isset($res['data']['emails'][0]) &&
                    $res['data']['emails'][0]
                ) {
                    foreach ($res['data']['emails'] as $mail) {
                        if ($debug) {
                            echo '.';
                        }

                        $filter['last_id'] = $mail['ei_id'] + 1;

                        $find = Email::find()->where(
                            [
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
                        if (isset($mail['ei_email_from_name'])) {
                            $email->e_email_from_name = $this->filter($mail['ei_email_from_name']);
                        }
                        if (isset($mail['ei_email_subject'])) {
                            $email->e_email_subject = $this->filter($mail['ei_email_subject']);
                        }
                        if ($dep = DepartmentEmailProject::find()->byEmail($mail['ei_email_to'])->one()) {
                            $email->e_project_id = $dep->dep_project_id;
                        } else if ($upp = UserProjectParams::find()->byEmail($mail['ei_email_to'])->one()) {
                            $email->e_project_id = $upp->upp_project_id;
                        } else {
                            $email->e_project_id = null;
                        }
                        $email->body_html = $mail['ei_email_text'];
                        $email->e_created_dt = $mail['ei_created_dt'];

                        $email->e_inbox_email_id = $mail['ei_id'];
                        $email->e_inbox_created_dt = $mail['ei_received_dt'] ?: $mail['ei_created_dt'];
                        $email->e_ref_message_id = $mail['ei_ref_mess_ids'];
                        $email->e_message_id = $mail['ei_message_id'];

                        $email->e_client_id = $this->emailService->detectClientId($email->e_email_from);

                        $lead_id = $this->emailService->detectLeadId($email);
                        $case_id = $this->emailService->detectCaseId($email);

                        $users = $email->getUsersIdByEmail();

                        $user_id = 0;
                        if ($users) {
                            foreach ($users as $user_id) {
                                $userArray[$user_id] = $user_id;
                            }
                        }

                        if ($user_id > 0) {
                            $email->e_created_user_id = $user_id;
                        }

                        if ($lead_id) {
                            // \Yii::info('Email Detected LeadId ' . $lead_id . ' from ' . $email->e_email_from, 'info\ReceiveEmailsJob:execute');
                            $leadArray[$lead_id] = $lead_id;
                        }

                        if ($case_id) {
                            $caseArray[$case_id] = $case_id;
                            CaseEventLog::add($case_id, null, 'Email received from customer');
                        }

                        if (!$email->save()) {
                            \Yii::error(VarDumper::dumpAsString([
                                'communicationId' => $mail['ei_id'],
                                'error' => $email->errors,
                            ]), 'DownloadEmails:emailSave');
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
                                    $email->e_client_id = $this->emailService->detectClientId($email->e_email_from);
                                    $email->save(false);
                                    if ($email->e_case_id) {
                                        CaseEventLog::add($email->e_case_id, null, 'Email received from customer');
                                    }
                                } catch (\Throwable $e) {
                                    Yii::error($e->getMessage(), 'DownloadEmails:EmailIncomingService:create');
                                }
                            }
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
                                Yii::error(AppHelper::throwableFormatter($throwable), 'DownloadEmails:createSaleFromBo:addToJobFailed');
                            }
                        }

                        if ($email->e_case_id && ($case = Cases::findOne($email->e_case_id))) {
                            $userID = $email->getUserIdByEmail($email->e_email_to);
                            if ($userID) {
                                $userCase = ['user' => $userID, 'case_short_link' => Purifier::createCaseShortLink($case)];
                                $notifyByCases[] = $userCase;
                            }
                        }

                        if ($email->e_lead_id && ($lead = Lead::findOne($email->e_lead_id))) {
                            $userID = $email->getUserIdByEmail($email->e_email_to);
                            if ($userID) {
                                $userLead = ['user' => $userID, 'lead_short_link' => Purifier::createLeadShortLink($lead)];
                                $notifyByLeads[] = $userLead;
                            }

                            try {
                                if (!LeadDataCreateService::isExist($lead->id, LeadDataKeyDictionary::KEY_WE_EMAIL_REPLIED)) {
                                    (new LeadDataCreateService())->createWeEmailReplied($lead);
                                    $job = new WebEngageLeadRequestJob($lead->id, WebEngageDictionary::EVENT_LEAD_EMAIL_REPLIED);
                                    Yii::$app->queue_job->priority(100)->push($job);
                                }
                            } catch (\Throwable $throwable) {
                                Yii::warning(
                                    AppHelper::throwableLog($throwable),
                                    'DownloadEmails:download:we_email_replied'
                                );
                            }
                        }

                        if ($attachPaths = ArrayHelper::getValue($mail, 'attach_paths')) {
                            $emailDataAttachments = [];
                            foreach (explode(',', $attachPaths) as $path) {
                                if (!$this->fileSystem->fileExists($path)) {
                                    \Yii::warning(VarDumper::dumpAsString([
                                        'communicationId' => $mail['ei_id'],
                                        'error' => 'File not exist : ' . $path,
                                    ]), 'DownloadEmails:Attach:fileExists');
                                    continue;
                                }

                                $createByApiDto = new CreateByApiDto($path, $this->fileSystem);
                                $fileStorage = FileStorage::createByEmail($createByApiDto);
                                $this->fileStorageRepository->save($fileStorage);

                                $emailDataAttachments['files'][] = [
                                    'value' => $fileStorage->fs_path,
                                    'name' => $fileStorage->fs_name,
                                    'type_id' => $fileStorage->fs_private ? $this->urlGenerator::TYPE_PRIVATE : $this->urlGenerator::TYPE_PUBLIC,
                                ];

                                if ($email->e_client_id && $fileStorage->fs_id) {
                                    $fileClient = FileClient::create($fileStorage->fs_id, $email->e_client_id);
                                    $this->fileClientRepository->save($fileClient);
                                }
                                if ($email->e_case_id && $fileStorage->fs_id) {
                                    $fileCase = FileCase::create($fileStorage->fs_id, $email->e_case_id);
                                    $this->fileCaseRepository->save($fileCase);
                                }
                                if ($email->e_lead_id && $fileStorage->fs_id) {
                                    $fileLead = FileLead::create($fileStorage->fs_id, $email->e_lead_id);
                                    $this->fileLeadRepository->save($fileLead);
                                }
                            }
                            if ($emailDataAttachments) {
                                $email->e_email_data = json_encode($emailDataAttachments);
                                if (!$email->save()) {
                                    \Yii::error(VarDumper::dumpAsString([
                                        'communicationId' => $mail['ei_id'],
                                        'error' => $email->errors,
                                    ]), 'DownloadEmails:saveAttachEmailData');
                                }
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
                    $cycleCount--;
                    $accessEmailRequest = false;
                    if ($debug) {
                        echo 'Cycle finish' . PHP_EOL;
                    }
                }
                $cycleCount++;
            }

            if ($notifyByCases) {
                foreach ($notifyByCases as $data) {
                    if ($ntf = Notifications::create($data['user'], 'New Email Received', 'New Email Received. Case(' . $data['case_short_link'] . ').', Notifications::TYPE_INFO, true)) {
                        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                        Notifications::publish('getNewNotification', ['user_id' => $data['user']], $dataNotification);
                    }
                }
            }

            if ($notifyByLeads) {
                foreach ($notifyByLeads as $data) {
                    if ($ntf = Notifications::create($data['user'], 'New Email Received', 'New Email Received. Lead(' . $data['lead_short_link'] . ').', Notifications::TYPE_INFO, true)) {
                        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                        Notifications::publish('getNewNotification', ['user_id' => $data['user']], $dataNotification);
                    }
                }
            }

            /*if ($userArray) {
                foreach ($userArray as $user_id) {
                    if ($ntf = Notifications::create($user_id, 'New Emails received', 'New Emails received. Check your inbox.', Notifications::TYPE_INFO, true)) {
                        // Notifications::socket($user_id, null, 'getNewNotification', [], true);
                        $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                        Notifications::publish('getNewNotification', ['user_id' => $user_id], $dataNotification);
                    }
                }
            }*/

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
            if ($debug) {
                echo "error: " . VarDumper::dumpAsString($e);
            }
            \Yii::error($e, 'DownloadEmails:throwable');
        }
        if ($debug) {
            echo "cycleCount:" . $cycleCount . " countTotal:" . $countTotal . PHP_EOL;
        }
    }

    private function filter($str)
    {
        if (!$str) {
            return $str;
        }
        return filter_var($str, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    }

    private function getEmailsForReceivedMessages(): array
    {
        $mailsUpp = UserProjectParams::find()->select('el_email')->distinct()->joinWith('emailList', false, 'INNER JOIN')->column();
        $mailsDep = DepartmentEmailProject::find()->select(['el_email'])->distinct()->joinWith('emailList', false, 'INNER JOIN')->column();
        return array_merge($mailsUpp, $mailsDep);
    }
}

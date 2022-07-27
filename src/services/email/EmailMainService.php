<?php

namespace src\services\email;

use modules\featureFlag\FFlag;
use Yii;
use frontend\models\EmailPreviewFromInterface;
use common\models\Lead;
use src\entities\cases\Cases;
use src\forms\emailReviewQueue\EmailReviewQueueForm;
use src\dto\email\EmailDTO;
use src\services\email\incoming\EmailIncomingService;
use src\entities\email\Email as EmailNorm;
use common\models\Email;
use src\repositories\email\EmailRepository;
use src\services\cases\CasesManageService;
use common\components\jobs\CreateSaleFromBOJob;
use src\helpers\app\AppHelper;
use common\components\purifier\Purifier;
use src\model\leadData\services\LeadDataCreateService;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use common\components\jobs\WebEngageLeadRequestJob;
use modules\webEngage\settings\WebEngageDictionary;
use src\entities\cases\CaseEventLog;
use src\entities\email\helpers\EmailStatus;
use src\services\abtesting\email\EmailTemplateOfferABTestingService;
use src\entities\email\EmailInterface;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadUserData\entity\LeadUserData;
use src\model\leadUserData\entity\LeadUserDataDictionary;
use src\model\leadUserData\repository\LeadUserDataRepository;
use yii\helpers\VarDumper;

/**
 *
 * Class EmailMainService
 *
 * @property EmailService $oldService
 * @property EmailsNormalizeService $normalizedService
 * @property EmailServiceHelper $helper
 *
 */
class EmailMainService implements EmailServiceInterface
{
    private $oldService;
    private $normalizedService;
    private $helper;
    private $emailRepository;

    private $emailObj;
    private $emailNormObj;

    public function __construct()
    {
        $this->emailRepository = Yii::createObject(EmailRepository::class);
        $this->helper = Yii::createObject(EmailServiceHelper::class);
        $this->oldService = Yii::createObject(EmailService::class);
        $this->normalizedService = Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE) ?
            EmailsNormalizeService::newInstance() :
            null
        ;
    }

    public static function newInstance()
    {
        return new static();
    }

    private function setEmailObj(Email $email)
    {
        $this->emailObj = $email;
    }

    private function getEmailObj()
    {
        return $this->emailObj;
    }

    private function setEmailNormObj(EmailNorm $email)
    {
        $this->emailNormObj = $email;
    }

    private function getEmailNormObj()
    {
        return $this->emailNormObj;
    }

    public function sendMail(EmailInterface $email, array $data = [])
    {
        try {
            if ($this->normalizedService !== null) {
                $requestData = $this->normalizedService->sendMail($this->getEmailNormObj(), $data);
                $this->normalizedService->updataAfterSendMail($this->getEmailNormObj(), $requestData);
            } else {
                $requestData = $this->oldService->sendMail($this->getEmailObj(), $data);
            }

            if ($this->getEmailObj()) {
                $this->oldService->updataAfterSendMail($this->getEmailObj(), $requestData);
            }

            $email = $this->getEmailNormObj() ?? $this->getEmailObj() ?? $email;
            $this->addToABTesting($email);
            $tplType = $email->templateType ? $email->templateType->etp_key : null;
            $this->leadProcessAfterEmailSending($email->e_id, $tplType, $email->lead);
        } catch (\Throwable $exception) {
            $error = VarDumper::dumpAsString($exception->getMessage());
            \Yii::error($error, 'EmailMainService:sendMail:exception');

            if ($this->getEmailObj()) {
                $this->getEmailObj()->statusToError('Communication error: ' . $error);
            }
            if ($this->getEmailNormObj()) {
                $this->getEmailNormObj()->statusToError('Communication error: ' . $error);
            }
            throw new \RuntimeException($error);
        }
    }

    private function addToABTesting(EmailInterface $email)
    {
        /** @fflag FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES, A/B testing for email offer templates enable/disable */
        if (EmailStatus::notError($email->e_status_id) && Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES)) {
            $templateTypeId = $email->getTemplateTypeId();
            $projectId = $email->getProjectId();
            $departmentId = $email->getDepartmentId();
            if ($templateTypeId && $projectId && $departmentId) {
                EmailTemplateOfferABTestingService::incrementCounterByTemplateAndProjectIds(
                    $templateTypeId,
                    $projectId,
                    $departmentId
                );
            }
        }
    }

    private function leadProcessAfterEmailSending(int $emailId, ?string $tplType, ?Lead $lead)
    {
        if ($emailId && $lead && LeadPoorProcessingService::checkEmailTemplate($tplType)) {
            LeadPoorProcessingService::addLeadPoorProcessingRemoverJob(
                $lead->id,
                [
                    LeadPoorProcessingDataDictionary::KEY_NO_ACTION,
                    LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE,
                    LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER,
                ],
                LeadPoorProcessingLogStatus::REASON_EMAIL
            );

            if ($lead->employee_id && $lead->isProcessing()) {
                try {
                    $leadUserData = LeadUserData::create(
                        LeadUserDataDictionary::TYPE_EMAIL_OFFER,
                        $lead->id,
                        $lead->employee_id,
                        (new \DateTimeImmutable())
                    );
                    (new LeadUserDataRepository($leadUserData))->save(true);
                } catch (\RuntimeException | \DomainException $throwable) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['emailId' => $emailId]);
                    \Yii::warning($message, 'EmailMainService:leadProcessAfterEmailSending:Exception');
                } catch (\Throwable $throwable) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['emailId' => $emailId]);
                    \Yii::error($message, 'EmailMainService:leadProcessAfterEmailSending:Throwable');
                }
            }
        }
    }

    public function createFromLead(EmailPreviewFromInterface $previewEmailForm, Lead $lead, array $attachments = [])
    {
        $email = $this->oldService->createFromLead($previewEmailForm, $lead, $attachments);
        $email->refresh();
        $this->setEmailObj($email);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromLead($previewEmailForm, $lead, $attachments);
            $email->refresh();
            $this->setEmailNormObj($email);
        }

        return $email;
    }

    public function createFromCase(EmailPreviewFromInterface $previewEmailForm, Cases $case, array $attachments = [])
    {
        $email = $this->oldService->createFromCase($previewEmailForm, $case, $attachments);
        $email->refresh();
        $this->setEmailObj($email);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromCase($previewEmailForm, $case, $attachments);
            $email->refresh();
            $this->setEmailNormObj($email);
        }

        return $email;
    }


    public function updateAfterReview(EmailReviewQueueForm $form, $email)
    {
        $email = $this->oldService->updateAfterReview($form, $email);
        $email->refresh();
        $this->setEmailObj($email);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->updateAfterReview($form, $email);
            $email->refresh();
            $this->setEmailNormObj($email);
        }

        return $email;
    }

    public function createFromDTO(EmailDTO $emailDTO, $autoDetectEmpty = true)
    {
        $email = $this->oldService->createFromDTO($emailDTO, $autoDetectEmpty);
        $email->refresh();
        $this->setEmailObj($email);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromDTO($emailDTO, $autoDetectEmpty);
            $email->refresh();
            $this->setEmailNormObj($email);
        }

        return $email;
    }

    public function receiveEmail(EmailDTO $emailDTO)
    {
        $notifications = [];
        $email = $this->oldService->createFromDTO($emailDTO, true);
        $email->refresh();
        $this->setEmailObj($email);

        if (!$email->hasLead() && !$email->hasCase() && EmailServiceHelper::isNotInternalEmail($emailDTO->emailFrom)) {
            $process = $this->processIncoming($email);
            $this->linkLeadCase($email, $process->leadId, $process->caseId);
        }

        $userID = $email->e_created_user_id;
        $case = $email->case ?? null;
        if ($case) {
            (Yii::createObject(CasesManageService::class))->needAction($case->cs_id);
            $this->addCreateSaleJob($case, $emailDTO->emailFrom);

            if ($userID) {
                $notifyData = [
                    'user' => $userID,
                    'message' => 'New Email Received. Case(' . Purifier::createCaseShortLink($case) . ').'
                ];
                array_push($notifications, $notifyData);
            }
        }

        $lead = $email->lead ?? null;
        if ($lead) {
            if ($userID) {
                $notifyData = [
                    'user' => $userID,
                    'title' => 'New Email Received',
                    'message' => 'New Email Received. Lead(' . Purifier::createLeadShortLink($lead) . ').'
                ];
                array_push($notifications, $notifyData);
            }
            $this->addCreateLeadDataJob($lead);
        }

        if (!empty($emailDTO->attachPaths)) {
            $attachments = $this->getAttachmentsArray($email, $emailDTO->attachPaths, $emailDTO->inboxEmailId);
            if (!empty($attachments)) {
                $email->updateEmailData($attachments);
            }
        }

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromDTO($emailDTO, true);
            $email->refresh();
            $this->setEmailNormObj($email);
            if (isset($process)) {
                $this->linkLeadCase($email, $process->leadId, $process->caseId);
            }
            if (isset($attachments)) {
                $email->updateEmailData($attachments);
            }
        }

        return $notifications;
    }

    public function getAttachmentsArray($email, array $attachPaths, int $communicationId)
    {
        $attachmentsService = new AttachmentsService($email);
        $emailDataAttachments = [];

        foreach ($attachPaths as $path) {
            $file = $attachmentsService->processingFile($path);
            if ($file === null) {
                Yii::warning(VarDumper::dumpAsString([
                    'communicationId' => $communicationId,
                    'error' => 'File not exist : ' . $path,
                ]), 'EmailMainService:getAttachmentsArray');
            } else {
                array_push($emailDataAttachments['files'], $file);
            }
        }

        return $emailDataAttachments;
    }

    public function addCreateSaleJob(Cases $case, string $emailFrom)
    {
        try {
            $job = new CreateSaleFromBOJob();
            $job->case_id = $case->cs_id;
            $job->email = $emailFrom;
            $job->project_key = $case->project->api_key ?? null;
            Yii::$app->queue_job->priority(100)->push($job);
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable), 'EmailMainService:addCreateSaleJob');
        }
    }

    public function addCreateLeadDataJob(Lead $lead)
    {
        try {
            if (!LeadDataCreateService::isExist($lead->id, LeadDataKeyDictionary::KEY_WE_EMAIL_REPLIED)) {
                (new LeadDataCreateService())->createWeEmailReplied($lead);
                $job = new WebEngageLeadRequestJob($lead->id, WebEngageDictionary::EVENT_LEAD_EMAIL_REPLIED);
                Yii::$app->queue_job->priority(100)->push($job);
            }
        } catch (\Throwable $throwable) {
            Yii::warning(AppHelper::throwableLog($throwable), 'EmailMainService::addCreateLeadDataJob');
        }
    }

    public function linkLeadCase($email, ?int $leadId, ?int $caseId)
    {
        if ($email instanceof Email) {
            $email->updateAttributes(['e_lead_id' => $leadId, 'e_case_id' => $caseId]);
        } else {
            if ($leadId) {
                $this->emailRepository->linkLeads($email, [$leadId]);
            }
            if ($caseId) {
                $this->emailRepository->linkCases($email, [$caseId]);
            }
        }

        if ($caseId) {
            CaseEventLog::add($caseId, null, 'Email received from customer');
        }
    }

    /**
     *
     * @param Email|EmailNorm $email
     */
    public function processIncoming($email)
    {
        $emailIncomingService = Yii::createObject(EmailIncomingService::class);
        return $emailIncomingService->create(
            $email->e_id,
            $email->getEmailFrom(false),
            $email->getEmailTo(false),
            $email->e_project_id
        );
    }
}

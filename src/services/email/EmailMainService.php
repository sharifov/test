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
use src\entities\email\EmailRepository;
use src\services\cases\CasesManageService;
use common\components\jobs\CreateSaleFromBOJob;
use src\helpers\app\AppHelper;
use common\components\purifier\Purifier;
use src\model\leadData\services\LeadDataCreateService;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use common\components\jobs\WebEngageLeadRequestJob;
use modules\webEngage\settings\WebEngageDictionary;
use src\entities\cases\CaseEventLog;

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

    public function sendMail($email, array $data = [])
    {
        if ($this->normalizedService !== null) {
            $this->normalizedService->sendMail($email, $data);
        } else {
            $this->oldService->sendMail($email, $data);
            //TODO: if called norm service need to change status,error message to older version. So need to devide method to smaller parts
        }
    }

    public function createFromLead(EmailPreviewFromInterface $previewEmailForm, Lead $lead, array $attachments = [])
    {
        $email = $this->oldService->createFromLead($previewEmailForm, $lead, $attachments);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromLead($previewEmailForm, $lead, $attachments);
        }

        return $email;
    }

    public function createFromCase(EmailPreviewFromInterface $previewEmailForm, Cases $case, array $attachments = [])
    {
        $email = $this->oldService->createFromCase($previewEmailForm, $case, $attachments);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromCase($previewEmailForm, $case, $attachments);
        }

        return $email;
    }


    public function updateAfterReview(EmailReviewQueueForm $form, $email)
    {
        $email = $this->oldService->updateAfterReview($form, $email);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->updateAfterReview($form, $email);
        }

        return $email;
    }

    public function createFromDTO(EmailDTO $emailDTO, $autoDetectEmpty = true)
    {
        $email = $this->oldService->createFromDTO($emailDTO, $autoDetectEmpty);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromDTO($emailDTO, $autoDetectEmpty);
        }

        return $email;
    }

    public function receiveEmail(EmailDTO $emailDTO)
    {
        $notifications = [];
        $email = $this->oldService->createFromDTO($emailDTO, true);

        if (!$email->hasLead() && !$email->hasCase() && $this->helper->isNotInternalEmail($emailDTO->emailFrom)) {
            $process = $this->processIncoming($email);
            $this->linkLeadCase($email, $process->leadId, $process->caseId);
        }

        $userID = $email->e_created_user_id;
        $case = $email->eCase ?? $email->case;
        if ($case) {
            (Yii::createObject(CasesManageService::class))->needAction($case->cs_id);
            $this->addCreateSaleJob($case->cs_id, $emailDTO->emailFrom);

            if ($userID) {
                $notifyData = [
                    'user' => $userID,
                    'message' => 'New Email Received. Case(' . Purifier::createCaseShortLink($case) . ').'
                ];
                array_push($notifications, $notifyData);
            }
        }

        $lead = $email->eLead ?? $email->lead;
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

    public function addCreateSaleJob(int $caseId, string $emailFrom)
    {
        try {
            $job = new CreateSaleFromBOJob();
            $job->case_id = $caseId;
            $job->email = $emailFrom;
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

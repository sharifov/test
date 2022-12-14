<?php

namespace src\services\email;

use modules\featureFlag\FFlag;
use modules\objectTask\src\scenarios\NoAnswer;
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
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use modules\lead\src\services\LeadTaskListService;
use common\components\jobs\UserTaskCompletionJob;
use modules\taskList\src\entities\TargetObject;
use src\auth\Auth;
use modules\taskList\src\entities\TaskObject;
use src\entities\email\form\EmailForm;
use src\repositories\email\EmailOldRepository;
use src\services\cases\CasesCommunicationService;
use src\model\emailReviewQueue\EmailReviewQueueManageService;
use src\model\emailReviewQueue\entity\EmailReviewQueue;
use yii\web\NotFoundHttpException;

/**
 *
 * Class EmailMainService
 *
 * @property EmailService $oldService
 * @property EmailsNormalizeService|null $normalizedService
 * @property EmailServiceHelper $helper
 * @property EmailOldRepository $emailOldRepository
 * @property EmailRepository $emailRepository
 *
 * @property Email|null $emailObj
 * @property EmailNorm|null $emailNormObj
 *
 */
class EmailMainService implements EmailServiceInterface
{
    public const FROM_OLD = 1; //CALLED FROM OLD EMAIL MODEL
    public const FROM_NORM = 2; //CALLED FROM NORM EMAIL MODEL

    private EmailService $oldService;
    private ?EmailsNormalizeService $normalizedService;
    private EmailRepository $emailRepository;
    private EmailOldRepository $emailOldRepository;

    private $emailObj;
    private $emailNormObj;

    public function __construct(
        EmailRepository $emailRepository,
        EmailOldRepository $emailOldRepo,
        EmailService $emailService
    ) {
        $this->emailRepository = $emailRepository;
        $this->emailOldRepository = $emailOldRepo;
        $this->oldService = $emailService;
        $this->normalizedService = Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE) ?
            EmailsNormalizeService::newInstance() :
            null
        ;
    }

    /**
     * @return static
     * @throws \yii\base\InvalidConfigException
     */
    public static function newInstance()
    {
        $emailRepository = Yii::createObject(EmailRepository::class);
        $emailOldRepository = Yii::createObject(EmailOldRepository::class);
        $oldService = Yii::createObject(EmailService::class);

        return new static($emailRepository, $emailOldRepository, $oldService);
    }

    /**
     * @param int $emailId
     * @return Email|null
     */
    private function setEmailObjById(int $emailId): ?Email
    {
        $this->emailObj = $this->emailOldRepository->find($emailId);
        return $this->emailObj;
    }

    /**
     * @param Email $email
     * @return void
     */
    private function setEmailObj(Email $email): void
    {
        $this->emailObj = $email;
    }

    /**
     * @return Email|null
     */
    private function getEmailObj(): ?Email
    {
        return $this->emailObj;
    }

    /**
     * @param int $emailId
     * @return EmailNorm|null
     */
    private function setEmailNormObjById(int $emailId): ?EmailNorm
    {
        try {
            $this->emailNormObj = $this->emailRepository->find($emailId);
        } catch (\Throwable $e) {
        }

        return $this->emailNormObj;
    }

    /**
     * @param EmailNorm $email
     * @return void
     */
    private function setEmailNormObj(EmailNorm $email): void
    {
        $this->emailNormObj = $email;
    }

    /**
     * @return EmailNorm|null
     */
    private function getEmailNormObj(): ?EmailNorm
    {
        return $this->emailNormObj;
    }

    /**
     * @param Email|EmailNorm|null $email
     * @return int|null
     */
    private function getCalledFrom($email = null): ?int
    {
        return ($email instanceof Email) ? self::FROM_OLD : self::FROM_NORM;
    }

    /**
     * @param Email|EmailNorm $email
     * @param array $data
     * @return void
     */
    public function sendMail($email, array $data = [])
    {
        try {
            $calledFrom = $this->getCalledFrom($email);
            if ($calledFrom == self::FROM_NORM) {
                if ($this->getEmailNormObj() == null) {
                    $this->setEmailNormObj($email);
                } elseif ($this->getEmailObj() == null) {
                    $this->setEmailObjById($email->e_id);
                }
            } else {
                if ($this->getEmailNormObj() == null) {
                    $this->setEmailNormObjById($email->e_id);
                } elseif ($this->getEmailObj() == null) {
                    $this->setEmailObj($email);
                }
            }

            if ($this->normalizedService !== null && $this->getEmailNormObj() !== null) {
                $requestData = $this->normalizedService->sendMail($this->getEmailNormObj(), $data);
                $this->normalizedService->updateAfterSendMail($this->getEmailNormObj(), $requestData);
            } else {
                $requestData = $this->oldService->sendMail($this->getEmailObj(), $data);
            }

            if ($this->getEmailObj()) {
                $this->oldService->updateAfterSendMail($this->getEmailObj(), $requestData);
            }

            $email = $this->getEmailNormObj() ?? $this->getEmailObj() ?? $email;
            $this->addToABTesting($email);
            $tplType = $email->templateType ? $email->templateType->etp_key : null;
            $this->leadProcessAfterEmailSending($email->e_id, $tplType, $email->lead);
        } catch (\Throwable $exception) {
            $error = VarDumper::dumpAsString($exception->getMessage());
            \Yii::error([
                    'emailId' => $email->e_id,
                    'message' => $error
                ], 'EmailMainService:sendMail:exception');

            if ($this->getEmailObj()) {
                $this->getEmailObj()->statusToError('Communication error: ' . $error);
            }
            if ($this->getEmailNormObj()) {
                $this->getEmailNormObj()->statusToError('Communication error: ' . $error);
            }
            throw new \RuntimeException($error);
        }
    }

    /**
     * @param int $emailId
     * @param Lead|null $lead
     * @param bool $useOwner
     * @return void
     */
    public function leadTaskJob(int $emailId, ?Lead $lead, bool $useOwner = false)
    {
        if ($emailId && $lead && (new LeadTaskListService($lead))->isProcessAllowed()) {
            $job = new UserTaskCompletionJob(
                TargetObject::TARGET_OBJ_LEAD,
                $lead->id,
                TaskObject::OBJ_EMAIL,
                $emailId,
                ($useOwner && $lead->employee_id) ? $lead->employee_id : Auth::id()
            );
            \Yii::$app->queue_job->push($job);
        }
    }

    /**
     * @param EmailInterface $email
     * @return void
     */
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

    /**
     * @param int $emailId
     * @param string|null $tplType
     * @param Lead|null $lead
     * @return void
     */
    private function leadProcessAfterEmailSending(int $emailId, ?string $tplType, ?Lead $lead)
    {
        if ($emailId && $lead && $tplType && LeadPoorProcessingService::checkEmailTemplate($tplType)) {
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

    /**
     * @param Email|EmailNorm $email
     * @param EmailForm $form
     * @return Email|EmailNorm
     * @throws Yii\db\Exception
     * @throws \Throwable
     */
    public function update($email, EmailForm $form)
    {
        $calledFrom = $this->getCalledFrom($email);
        $emailOld = ($calledFrom == self::FROM_NORM) ? $this->setEmailObjById($email->e_id) : $email;

        $emailOld = $this->oldService->update($emailOld, $form);
        $emailOld->refresh();
        $this->setEmailObj($emailOld);

        if ($this->normalizedService !== null) {
            $emailNorm = ($calledFrom == self::FROM_OLD) ? $this->setEmailNormObjById($email->e_id) : $email;
            if (isset($emailNorm)) {
                $emailNorm = $this->normalizedService->update($emailNorm, $form);
                $emailNorm->refresh();
                $this->setEmailNormObj($emailNorm);
            }
        }

        return ($calledFrom == self::FROM_OLD) ? $emailOld : $emailNorm ?? $email;
    }

    /**
     * @param EmailForm $form
     * @return Email|EmailNorm
     * @throws Yii\db\Exception
     * @throws Yii\db\StaleObjectException
     * @throws \Throwable
     */
    public function create(EmailForm $form)
    {
        $email = $this->oldService->create($form);
        $email->refresh();
        $this->setEmailObj($email);
        $form->emailId = $email->e_id;

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->create($form);
            $email->refresh();
            $this->setEmailNormObj($email);
        }

        return $email;
    }

    /**
     * @param EmailPreviewFromInterface $previewEmailForm
     * @param Lead $lead
     * @param array $attachments
     * @return Email|EmailNorm
     * @throws Yii\db\Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function createFromLead(EmailPreviewFromInterface $previewEmailForm, Lead $lead, array $attachments = [])
    {
        $email = $this->oldService->createFromLead($previewEmailForm, $lead, $attachments);
        $email->refresh();
        $this->setEmailObj($email);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromLead($previewEmailForm, $lead, $attachments, $email->e_id);
            $email->refresh();
            $this->setEmailNormObj($email);
        }

        return $email;
    }

    /**
     * @param EmailPreviewFromInterface $previewEmailForm
     * @param Cases $case
     * @param array $attachments
     * @return Email|EmailNorm
     * @throws Yii\db\Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function createFromCase(EmailPreviewFromInterface $previewEmailForm, Cases $case, array $attachments = [])
    {
        $email = $this->oldService->createFromCase($previewEmailForm, $case, $attachments);
        $email->refresh();
        $this->setEmailObj($email);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromCase($previewEmailForm, $case, $attachments, $email->e_id);
            $email->refresh();
            $this->setEmailNormObj($email);
        }

        return $email;
    }

    /**
     * @param EmailReviewQueueForm $form
     * @param Email|EmailNorm $email
     * @return Email|EmailNorm
     * @throws Yii\db\Exception
     * @throws \Throwable
     */
    public function updateAfterReview(EmailReviewQueueForm $form, $email)
    {
        $calledFrom = $this->getCalledFrom($email);
        $emailOld = ($calledFrom == self::FROM_NORM) ? $this->setEmailObjById($email->e_id) : $email;

        $emailOld = $this->oldService->updateAfterReview($form, $emailOld);
        $emailOld->refresh();
        $this->setEmailObj($emailOld);

        if ($this->normalizedService !== null) {
            $emailNorm = ($calledFrom == self::FROM_OLD) ? $this->setEmailNormObjById($email->e_id) : $email;
            if (isset($emailNorm)) {
                $emailNorm = $this->normalizedService->updateAfterReview($form, $emailNorm);
                $emailNorm->refresh();
                $this->setEmailNormObj($emailNorm);
            }
        }

        return ($calledFrom == self::FROM_OLD) ? $emailOld : $emailNorm ?? $email;
    }

    /**
     * @param EmailDTO $emailDTO
     * @param bool $autoDetectEmpty
     * @return Email|EmailNorm
     * @throws Yii\db\Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function createFromDTO(EmailDTO $emailDTO, bool $autoDetectEmpty = true)
    {
        $email = $this->oldService->createFromDTO($emailDTO, $autoDetectEmpty);
        $email->refresh();
        $this->setEmailObj($email);
        $emailDTO->emailId = $email->e_id;

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromDTO($emailDTO, $autoDetectEmpty);
            $email->refresh();
            $this->setEmailNormObj($email);
        }

        return $email;
    }

    /**
     * @param EmailDTO $emailDTO
     * @return array
     * @throws Yii\db\Exception
     * @throws Yii\db\StaleObjectException
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function receiveEmail(EmailDTO $emailDTO): array
    {
        $notifications = [];
        $email = $this->oldService->createFromDTO($emailDTO, true);
        $email->refresh();
        $this->setEmailObj($email);
        $emailDTO->emailId = $email->e_id;

        if (!$email->hasLead() && !$email->hasCase() && EmailServiceHelper::isNotInternalEmail($emailDTO->emailFrom)) {
            $process = $this->processIncoming($email);
            $this->linkLeadCase($email, $process->leadId, $process->caseId);
        }

        $userID = $email->e_created_user_id;
        $case = $email->case ?? null;
        if ($case) {
            (Yii::createObject(CasesCommunicationService::class))->processIncoming($case, CasesCommunicationService::TYPE_PROCESSING_EMAIL);

            (Yii::createObject(CasesManageService::class))->needAction($case->cs_id);
            $this->addCreateSaleJob($case, $emailDTO->emailFrom);

            if ($userID) {
                $notifications[] = [
                    'user' => $userID,
                    'message' => 'New Email Received. Case(' . Purifier::createCaseShortLink($case) . ').'
                ];
            }
        }

        $lead = $email->lead ?? null;
        if ($lead) {
            /** @fflag FFlag::FF_KEY_NO_ANSWER_PROTOCOL_ENABLE, No Answer protocol enable */
            if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_NO_ANSWER_PROTOCOL_ENABLE) === true) {
                NoAnswer::clientResponseLogicInit($lead);
            }

            if ($userID) {
                $notifications[] = [
                    'user' => $userID,
                    'title' => 'New Email Received',
                    'message' => 'New Email Received. Lead(' . Purifier::createLeadShortLink($lead) . ').'
                ];
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

    /**
     * @param $email
     * @param array $attachPaths
     * @param int $communicationId
     * @return array
     * @throws \League\Flysystem\FilesystemException
     * @throws \yii\base\InvalidConfigException
     */
    public function getAttachmentsArray($email, array $attachPaths, int $communicationId): array
    {
        $attachmentsService = new AttachmentsService($email);
        $emailDataAttachments = [];

        foreach ($attachPaths as $path) {
            $file = $attachmentsService->processingFile($path);
            if ($file === null) {
                Yii::warning(VarDumper::dumpAsString([
                    'emailId' => $email->e_id,
                    'communicationId' => $communicationId,
                    'error' => 'File not exist : ' . $path,
                ]), 'EmailMainService:getAttachmentsArray');
            } else {
                $emailDataAttachments['files'][] = $file;
            }
        }

        return $emailDataAttachments;
    }

    /**
     * @param Cases $case
     * @param string $emailFrom
     * @return void
     */
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

    /**
     * @param Lead $lead
     * @return void
     */
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

    /**
     * @param Email|EmailNorm $email
     * @param int|null $leadId
     * @param int|null $caseId
     * @return void
     */
    public function linkLeadCase($email, ?int $leadId, ?int $caseId)
    {
        if ($email instanceof Email) {
            $email->updateAttributes(['e_lead_id' => $leadId, 'e_case_id' => $caseId]);
        } else {
            if ($leadId) {
                $email->linkLeads([$leadId]);
            }
            if ($caseId) {
                $email->linkCases([$caseId]);
            }
        }

        if ($caseId) {
            CaseEventLog::add($caseId, null, 'Email received from customer');
        }
    }

    /**
     * @param $email
     * @return incoming\Process
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     */
    public function processIncoming($email): incoming\Process
    {
        $emailIncomingService = Yii::createObject(EmailIncomingService::class);
        return $emailIncomingService->create(
            $email->e_id,
            $email->getEmailFrom(false),
            $email->getEmailTo(false),
            $email->e_project_id
        );
    }

    /**
     *
     * @param int $communicationId
     * @param int $statusId
     * @return int|null
     * @throws NotFoundHttpException
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function updateEmailStatus(int $communicationId, int $statusId): ?int
    {
        if ($statusId <= 0) {
            throw new \RuntimeException('Email status not valid.');
        }
        if ($emailOld = $this->emailOldRepository->findByCommunicationId($communicationId)) {
            $this->oldService->changeStatus($emailOld, $statusId);
        }
        if ($this->normalizedService !== null) {
            if ($emailNorm = $this->emailRepository->findByCommunicationId($communicationId)) {
                $this->normalizedService->changeStatus($emailNorm, $statusId);
            }
        }

        return $emailOld ? $emailOld->e_id : ($emailNorm ? $emailNorm->e_id : null);
    }

    /**
     * @param string $messageId
     * @param string $emailTo
     * @param int $inboxId
     * @return bool
     */
    public function saveInboxId(string $messageId, string $emailTo, int $inboxId): bool
    {
        if ($emailOld = $this->emailOldRepository->findReceived($messageId, $emailTo)->limit(1)->one()) {
            $emailOld->saveInboxId($inboxId);
        }
        $emailNorm = null;
        if ($this->normalizedService !== null) {
            if ($emailNorm = $this->emailRepository->findReceived($messageId, $emailTo)->limit(1)->one()) {
                $emailNorm->saveInboxId($inboxId);
            }
        }

        return $emailOld || $emailNorm;
    }

    /**
     * @param $email
     * @return void
     */
    public function read($email)
    {
        $calledFrom = $this->getCalledFrom($email);
        $emailOld = ($calledFrom == self::FROM_NORM) ? $this->setEmailObjById($email->e_id) : $email;
        $emailOld->read();

        if ($this->normalizedService !== null) {
            $emailNorm = ($calledFrom == self::FROM_OLD) ? $this->setEmailNormObjById($email->e_id) : $email;
            if (isset($emailNorm)) {
                $emailNorm->read();
            }
        }
    }

    /**
     * @param Email|EmailNorm $email
     * @param int|null $departmentId
     * @return EmailReviewQueue
     * @throws InvalidConfigException
     */
    public function moveToReview($email, ?int $departmentId = null): EmailReviewQueue
    {
        $calledFrom = $this->getCalledFrom($email);
        $emailOld = ($calledFrom == self::FROM_NORM) ? $this->setEmailObjById($email->e_id) : $email;

        $emailOld->statusToReview();
        $emailOld->refresh();
        $this->setEmailObj($emailOld);

        if ($this->normalizedService !== null) {
            $emailNorm = ($calledFrom == self::FROM_OLD) ? $this->setEmailNormObjById($email->e_id) : $email;
            if (isset($emailNorm)) {
                $emailNorm->statusToReview();
                $emailNorm->refresh();
                $this->setEmailNormObj($emailNorm);
            }
        }

        $emailToReview = ($calledFrom == self::FROM_OLD) ? $emailOld : $emailNorm ?? $email;

        $emailReviewQueueManageService = Yii::createObject(EmailReviewQueueManageService::class);
        return $emailReviewQueueManageService->createByEmail($emailToReview, $departmentId);
    }


    /**
     * @param Email|EmailNorm $email
     * @param string $message
     * @return Email|EmailNorm
     */
    public function moveToCancel($email, string $message)
    {
        $calledFrom = $this->getCalledFrom($email);
        $emailOld = ($calledFrom == self::FROM_NORM) ? $this->setEmailObjById($email->e_id) : $email;

        $emailOld->statusToCancel($message);
        $emailOld->refresh();
        $this->setEmailObj($emailOld);

        if ($this->normalizedService !== null) {
            $emailNorm = ($calledFrom == self::FROM_OLD) ? $this->setEmailNormObjById($email->e_id) : $email;
            if (isset($emailNorm)) {
                $emailNorm->statusToCancel($message);
                $emailNorm->refresh();
                $this->setEmailNormObj($emailNorm);
            }
        }

        return ($calledFrom == self::FROM_OLD) ? $emailOld : $emailNorm ?? $email;
    }

    /**
     * @param Email|EmailNorm $email
     * @return void
     */
    public function softDelete($email)
    {
        $isDeleted = $email->isDeleted();

        $calledFrom = $this->getCalledFrom($email);
        $emailOld = ($calledFrom == self::FROM_NORM) ? $this->setEmailObjById($email->e_id) : $email;
        $emailOld->updateAttributes([
            'e_is_deleted' => !$isDeleted
        ]);

        if ($this->normalizedService !== null) {
            $emailNorm = ($calledFrom == self::FROM_OLD) ? $this->setEmailNormObjById($email->e_id) : $email;
            if (isset($emailNorm)) {
                $emailNorm->updateAttributes([
                    'e_is_deleted' => !$isDeleted
                ]);
            }
        }
    }
}

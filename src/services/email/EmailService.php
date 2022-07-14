<?php

namespace src\services\email;

use common\models\ClientEmail;
use common\models\DepartmentEmailProject;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\UserProjectParams;
use src\entities\cases\Cases;
use src\repositories\cases\CasesRepository;
use src\repositories\lead\LeadRepository;
use src\repositories\NotFoundException;
use Yii;
use yii\helpers\VarDumper;
use src\exception\CreateModelException;
use src\entities\email\helpers\EmailStatus;
use src\services\abtesting\email\EmailTemplateOfferABTestingService;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadUserData\entity\LeadUserData;
use src\model\leadUserData\repository\LeadUserDataRepository;
use src\helpers\app\AppHelper;
use src\exception\EmailNotSentException;
use modules\featureFlag\FFlag;
use src\forms\emailReviewQueue\EmailReviewQueueForm;
use frontend\models\EmailPreviewFromInterface;
use src\dto\email\EmailDTO;

/**
 * Class EmailService
 *
 * @property LeadRepository $leadRepository
 * @property CasesRepository $casesRepository
 */
class EmailService implements EmailServiceInterface
{
    /**
     * @var LeadRepository
     */
    private $leadRepository;

    /**
     * @var CasesRepository
     */
    private $casesRepository;

    /**
     * EmailService constructor.
     * @param LeadRepository $leadRepository
     * @param CasesRepository $casesRepository
     */
    public function __construct(LeadRepository $leadRepository, CasesRepository $casesRepository)
    {
        $this->leadRepository = $leadRepository;
        $this->casesRepository = $casesRepository;
    }

    /**
     * @param Email $email
     * @return int|null
     */
    public function detectLeadId(Email $email): ?int
    {
        $subject = $email->e_email_subject;

        try {
            $lead = $this->getLeadFromSubjectByIdOrHash($subject);

            if (!$lead && !$lead = $this->getLeadFromSubjectByKivTag($email->e_ref_message_id)) {
//              $lead = $this->getLeadByLastEmail($email->e_email_from);
            }
        } catch (NotFoundException $exception) {
            Yii::info('(' . $exception->getCode() . ') ' . $exception->getMessage() . ' File: ' . $exception->getFile() . '(Line: ' . $exception->getLine() . ')' . '; message_id: ' . $email->e_ref_message_id, 'info\SalesEmailService:detectLeadId:NotFoundException');
        }

        $email->e_lead_id = $lead->id ?? null;

        return $email->e_lead_id;
    }

    /**
     * @param Email $email
     * @return int|null
     */
    public function detectCaseId(Email $email): ?int
    {
        $subject = $email->e_email_subject;

        try {
            $case = $this->getCaseFromSubjectByIdOrHash($subject);

            if (!$case && !$case = $this->getCaseFromSubjectByKivTag($email->e_ref_message_id)) {
//              $case = $this->getCaseByLastEmail($email->e_email_from);
            }
        } catch (NotFoundException $exception) {
            Yii::info('(' . $exception->getCode() . ') ' . $exception->getMessage() . ' File: ' . $exception->getFile() . '(Line: ' . $exception->getLine() . ')' . '; message_id: ' . $email->e_ref_message_id, 'info\SalesEmailService:detectCaseId:NotFoundException');
        }

        $email->e_case_id = $case->cs_id ?? null;

        return $email->e_case_id;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function isNotInternalEmail(string $email): bool
    {
        if (UserProjectParams::find()->byEmail($email)->one()) {
            return false;
        }
        if (Employee::find()->byEmail($email)->one()) {
            return false;
        }
        if (DepartmentEmailProject::find()->byEmail($email)->one()) {
            return false;
        }
        return true;
    }

    /**
     * @param string|null $subject
     * @return Cases
     */
    private function getCaseFromSubjectByIdOrHash(?string $subject): ?Cases
    {
        if (!$subject) {
            return null;
        }

        preg_match('~\[cid:(\d+)\]~si', $subject, $matches);

        if (!empty($matches[1])) {
            $case_id = (int) $matches[1];
            $case = $this->casesRepository->find($case_id);
        }

        if (empty($case)) {
            $matches = [];
            preg_match('~\[gid:(\w+)\]~si', $subject, $matches);
            if (!empty($matches[1])) {
                $case = $this->casesRepository->findByGid((int)$matches[1]);
            }
        }

        return $case ?? null;
    }

    /**
     * @param string|null $refMessageId
     * @return Cases|null
     */
    private function getCaseFromSubjectByKivTag(?string $refMessageId): ?Cases
    {
        if (!$refMessageId) {
            return null;
        }

        $matches = [];
        preg_match_all('~<kiv\.(.+)>~iU', $refMessageId, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $messageId) {
                $messageArr = explode('.', $messageId);
                $caseId = end($messageArr);
                if (!empty($caseId)) {
                    $case_id = (int) $caseId;
                    $case = $this->casesRepository->find($case_id);
                    if ($case) {
                        return $case;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param string $emailFrom
     * @return Cases|null
     */
    private function getCaseByLastEmail(string $emailFrom): ?Cases
    {
        $clientEmail = ClientEmail::find()->byEmail($emailFrom)->one();
        if (
            $clientEmail &&
            $clientEmail->client_id &&
            !$case = $this->casesRepository->getByClient($clientEmail->client_id)
        ) {
            $case = $this->casesRepository->getByClientWithAnyStatus($clientEmail->client_id);
        }

        return $case ?? null;
    }

    /**
     * @param string|null $subject
     * @return Lead|null
     */
    private function getLeadFromSubjectByIdOrHash(?string $subject): ?Lead
    {
        if (!$subject) {
            return null;
        }

        preg_match('~\[lid:(\d+)\]~si', $subject, $matches);

        if (!empty($matches[1])) {
            $lead_id = (int) $matches[1];
            $lead = $this->leadRepository->get($lead_id);
        }

        if (empty($lead)) {
            $matches = [];
            preg_match('~\[uid:(\w+)\]~si', $subject, $matches);
            if (!empty($matches[1])) {
                $lead = $this->leadRepository->getByUid((int)$matches[1]);
            }
        }

        return $lead ?? null;
    }

    /**
     * @param string|null $refMessageId
     * @return Lead|null
     */
    private function getLeadFromSubjectByKivTag(?string $refMessageId): ?Lead
    {
        if (!$refMessageId) {
            return null;
        }

        $matches = [];
        preg_match_all('~<kiv\.(.+)>~iU', $refMessageId, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $messageId) {
                $messageArr = explode('.', $messageId);
                if (!empty($messageArr[2])) {
                    $lead_id = (int) $messageArr[2];

                    $lead = $this->leadRepository->get($lead_id);
                    if ($lead) {
                        return $lead;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param string $emailFrom
     * @return Lead|null
     */
    private function getLeadByLastEmail(string $emailFrom): ?Lead
    {
        $clientEmail = ClientEmail::find()->byEmail($emailFrom)->one();
        if (
            $clientEmail &&
            $clientEmail->client_id &&
            !$lead = $this->leadRepository->getActiveByClientId($clientEmail->client_id)
        ) {
            $lead = $this->leadRepository->getByClientId($clientEmail->client_id);
        }

        return $lead ?? null;
    }

    public function detectClientId(string $email)
    {
        $clientEmail = ClientEmail::find()->byEmail($email)->one();

        return $clientEmail->client_id ?? null;
    }

    /**
     * @param string $body
     * @return string
     */
    public static function prepareEmailBody(string $body): string
    {
        return str_replace('class="editable"', 'class="editable" contenteditable="true" ', $body);
    }

    /**
     *
     * @param Email $email
     * @param array $data
     * @throws \RuntimeException|EmailNotSentException
     */
    public function sendMail($email, array $data = [])
    {
        /** @var CommunicationService $communication */
        $communication = Yii::$app->comms;
        $data['project_id'] = $email->e_project_id;

        $content_data['email_body_html'] = $email->getEmailBodyHtml();
        $content_data['email_body_text'] = $email->e_email_body_text;
        $content_data['email_subject'] = $email->e_email_subject;
        $content_data['email_reply_to'] = $email->e_email_from;
        $content_data['email_cc'] = $email->e_email_cc;
        $content_data['email_bcc'] = $email->e_email_bc;

        if ($email->e_email_from_name) {
            $content_data['email_from_name'] = $email->e_email_from_name;
        }

        if ($email->e_email_to_name) {
            $content_data['email_to_name'] = $email->e_email_to_name;
        }

        if ($email->e_message_id) {
            $content_data['email_message_id'] = $email->e_message_id;
        }

        $tplType = $email->eTemplateType ? $email->eTemplateType->etp_key : null;
        $language = $email->e_language_id ?? 'en-US';

        try {
            $request = $communication->mailSend($email->e_project_id, $tplType, $email->e_email_from, $email->e_email_to, $content_data, $data, $language, 0);

            if ($request && isset($request['data']['eq_status_id'])) {
                $email->e_status_id = $request['data']['eq_status_id'];
                $email->e_communication_id = $request['data']['eq_id'];
                $email->save();
            }

            if ($request && isset($request['error']) && $request['error']) {
                $errorData = @json_decode($request['error'], true);
                $errorMessage = $errorData['message'] ?: $request['error'];
                throw new EmailNotSentException($email->e_email_to, $errorMessage);
            }
            /** @fflag FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES, A/B testing for email offer templates enable/disable */
            if (EmailStatus::notError($email->e_status_id) && \Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES)) {
                if ($email->e_template_type_id && $email->e_project_id && isset($email->eLead)) {
                    EmailTemplateOfferABTestingService::incrementCounterByTemplateAndProjectIds(
                        $email->e_template_type_id,
                        $email->e_project_id,
                        $email->eLead->l_dep_id
                        );
                }
            }
            if ($email->e_id && $email->e_lead_id && LeadPoorProcessingService::checkEmailTemplate($tplType)) {
                LeadPoorProcessingService::addLeadPoorProcessingRemoverJob(
                    $email->e_lead_id,
                    [
                        LeadPoorProcessingDataDictionary::KEY_NO_ACTION,
                        LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE,
                        LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER,
                    ],
                    LeadPoorProcessingLogStatus::REASON_EMAIL
                    );

                if (($lead = $email->eLead) && $lead->employee_id && $lead->isProcessing()) {
                    try {
                        $leadUserData = LeadUserData::create(
                            LeadUserDataDictionary::TYPE_EMAIL_OFFER,
                            $lead->id,
                            $lead->employee_id,
                            (new \DateTimeImmutable())
                            );
                        (new LeadUserDataRepository($leadUserData))->save(true);
                    } catch (\RuntimeException | \DomainException $throwable) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['emailId' => $email->e_id]);
                        \Yii::warning($message, 'EmailService:LeadUserData:Exception');
                    } catch (\Throwable $throwable) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['emailId' => $email->e_id]);
                        \Yii::error($message, 'EmailService:LeadUserData:Throwable');
                    }
                }
            }
        } catch (\Throwable $exception) {
            $error = VarDumper::dumpAsString($exception->getMessage());
            \Yii::error($error, 'EmailService:sendMail:mailSend:exception');
            $email->statusToError('Communication error: ' . $error);
            throw new \RuntimeException($error);
        }
    }

    /**
     *
     * @param EmailPreviewFromInterface $previewEmailForm
     * @return \common\models\Email
     */
    public function createFromPreviewForm(EmailPreviewFromInterface $previewEmailForm, array $attachments = [])
    {
        $mail = new Email();
        $mail->e_template_type_id = $previewEmailForm->getEmailTemplateId() ?? null;
        $mail->e_type_id = Email::TYPE_OUTBOX;
        $mail->e_status_id = Email::STATUS_PENDING;
        $mail->e_email_subject = $previewEmailForm->getEmailSubject();
        $mail->body_html = $previewEmailForm->getEmailMessage();
        $mail->e_email_from = $previewEmailForm->getEmailFrom();
        $mail->e_email_from_name = $previewEmailForm->getEmailFromName();
        $mail->e_email_to_name = $previewEmailForm->getEmailToName();
        $mail->e_language_id = $previewEmailForm->getLanguageId() ?? null;
        $mail->e_email_to = $previewEmailForm->getEmailTo();
        $mail->e_created_dt = date('Y-m-d H:i:s');
        $mail->e_created_user_id = \Yii::$app->user->id;
        $mail->e_email_data = json_encode($attachments);

        return $mail;
    }

    public function createFromLead(EmailPreviewFromInterface $previewEmailForm, Lead $lead, array $attachments = []): Email
    {
        try {
            $mail = $this->createFromPreviewForm($previewEmailForm, $attachments);
            $mail->e_project_id = $lead->project_id;
            $mail->e_lead_id = $lead->id;

            if ($mail->save()) {
                $mail->e_message_id = $mail->generateMessageId();
                $mail->update();
            } else {
                throw new CreateModelException(get_class($mail), $mail->getErrors());
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        return $mail;
    }

    public function createFromCase(EmailPreviewFromInterface $previewEmailForm, Cases $case, array $attachments = []): Email
    {
        try {
            $mail = $this->createFromPreviewForm($previewEmailForm);
            $mail->e_project_id = $case->cs_project_id;
            $mail->e_case_id = $case->cs_id;
            $mail->e_email_data = json_encode($attachments);

            if ($mail->save()) {
                $mail->e_message_id = $mail->generateMessageId();
                $mail->update();
            } else {
                throw new CreateModelException(get_class($mail), $mail->getErrors());
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        return $mail;
    }

    public function updateAfterReview(EmailReviewQueueForm $form, $email)
    {
        try {
            $email->e_email_from = $form->emailFrom;
            $email->e_email_from_name = $form->emailFromName;
            $email->e_email_to = $form->emailTo;
            $email->e_email_to_name = $form->emailToName;
            $email->e_email_subject = $form->emailSubject;
            $email->e_status_id = Email::STATUS_PENDING;
            $email->body_html = $form->emailMessage;

            if (!$email->save()) {
                throw new \Exception($email->getErrorSummary(true)[0]);
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        return $email;
    }

    public function createFromDTO(EmailDTO $emailDTO): Email
    {
        try {
            $email = new Email();
            $email->e_type_id = $emailDTO->typeId;
            $email->e_status_id = $emailDTO->statusId;
            $email->e_is_new = $emailDTO->isNew;
            $email->e_email_to = $emailDTO->emailTo;
            $email->e_email_to_name = $emailDTO->emailToName;
            $email->e_email_from = $emailDTO->emailFrom;
            $email->e_email_from_name = $emailDTO->emailFromName;
            $email->e_email_subject = $emailDTO->emailSubject;
            $email->e_project_id = $emailDTO->projectId;
            $email->body_html = $emailDTO->bodyHtml;
            $email->e_created_dt = $emailDTO->createdDt;
            $email->e_inbox_email_id = $emailDTO->inboxEmailId;
            $email->e_inbox_created_dt = $emailDTO->inboxCreatedDt;
            $email->e_ref_message_id = $emailDTO->refMessageId;
            $email->e_message_id = $emailDTO->messageId;
            $email->e_client_id = $emailDTO->clientId;
            $email->e_language_id = $emailDTO->languageId;
            $email->e_template_type_id = $emailDTO->templateTypeId;

            if (!$email->save()) {
                throw new CreateModelException(get_class($email), $email->getErrors());
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        return $email;
    }
}

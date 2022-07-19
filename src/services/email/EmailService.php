<?php

namespace src\services\email;

use common\models\ClientEmail;
use common\models\DepartmentEmailProject;
use common\models\Email;
use common\models\Employee;
use common\models\Lead;
use common\models\UserProjectParams;
use frontend\models\EmailPreviewFromInterface;
use src\dto\email\EmailDTO;
use src\entities\cases\Cases;
use src\exception\CreateModelException;
use src\forms\emailReviewQueue\EmailReviewQueueForm;
use src\repositories\cases\CasesRepository;
use src\repositories\lead\LeadRepository;
use src\repositories\NotFoundException;
use Yii;
use src\entities\email\EmailInterface;

/**
 * Class EmailService
 *
 * @property LeadRepository $leadRepository
 * @property CasesRepository $casesRepository
 */
class EmailService extends SendMail implements EmailServiceInterface
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
     * @var EmailServiceHelper
     */
    private $helper;

    /**
     * EmailService constructor.
     * @param LeadRepository $leadRepository
     * @param CasesRepository $casesRepository
     */
    public function __construct(LeadRepository $leadRepository, CasesRepository $casesRepository, EmailServiceHelper $helper)
    {
        $this->leadRepository = $leadRepository;
        $this->casesRepository = $casesRepository;
        $this->helper = $helper;
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
     * @return array
     */
    protected function generateContentData(EmailInterface $email)
    {
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

        return $content_data;
    }


    public function sendMail(EmailInterface $email, array $data = [])
    {
        $contentData = $this->generateContentData($email);
        return $this->sendToCommunication($email, $contentData, $data);
    }

    /**
     *
     * @param Email $email
     * @param array $requestData
     */
    public function updataAfterSendMail(Email $email, $requestData)
    {
        if ($requestData && isset($requestData['eq_status_id'])) {
            $email->updateAttributes([
                'e_status_id' => $requestData['eq_status_id'],
                'e_communication_id' =>  $requestData['eq_id']
            ]);
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
        $mail->e_email_data = !empty($attachments) ? json_encode($attachments) : null;

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
            $mail = $this->createFromPreviewForm($previewEmailForm, $attachments);
            $mail->e_project_id = $case->cs_project_id;
            $mail->e_case_id = $case->cs_id;

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

    public function createFromDTO(EmailDTO $emailDTO, $autoDetectEmpty = true): Email
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
            $email->e_project_id = $emailDTO->projectId ?? $this->helper->getProjectIdByDepOrUpp($emailDTO->emailTo);
            $email->body_html = $emailDTO->bodyHtml;
            $email->e_created_dt = $emailDTO->createdDt;
            $email->e_inbox_email_id = $emailDTO->inboxEmailId;
            $email->e_inbox_created_dt = $emailDTO->inboxCreatedDt;
            $email->e_ref_message_id = $emailDTO->refMessageId;
            $email->e_message_id = $emailDTO->messageId;
            $email->e_language_id = $emailDTO->languageId;
            $email->e_template_type_id = $emailDTO->templateTypeId;
            $email->e_client_id = $emailDTO->clientId;
            $email->e_lead_id = $emailDTO->leadId;
            $email->e_email_data = !empty($emailDTO->attachments) ? json_encode($emailDTO->attachments) : null;
            if ($autoDetectEmpty) {
                $email->e_client_id = $emailDTO->clientId ?? $this->helper->detectClientId($emailDTO->emailFrom);
                $email->e_lead_id = $this->helper->detectLeadId($emailDTO->emailSubject, $emailDTO->refMessageId);
                $email->e_case_id = $this->helper->detectCaseId($emailDTO->emailSubject, $emailDTO->refMessageId);
                $email->e_created_user_id = $this->helper->getUserIdByEmail($emailDTO->emailTo);
            }

            if (!$email->save()) {
                throw new CreateModelException(get_class($email), $email->getErrors());
            } elseif ($email->e_message_id == null) {
                $mail->e_message_id = $mail->generateMessageId();
                $mail->update();
            }
        } catch (\Throwable $e) {
            throw $e;
        }

        return $email;
    }
}

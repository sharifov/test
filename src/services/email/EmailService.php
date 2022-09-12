<?php

namespace src\services\email;

use common\models\Email;
use common\models\Lead;
use Exception;
use frontend\models\EmailPreviewFromInterface;
use RuntimeException;
use src\dto\email\EmailDTO;
use src\entities\cases\Cases;
use src\entities\email\EmailInterface;
use src\entities\email\helpers\EmailStatus;
use src\entities\email\helpers\EmailType;
use src\exception\CreateModelException;
use src\forms\emailReviewQueue\EmailReviewQueueForm;
use src\entities\email\form\EmailForm;
use Yii;
use yii\db\StaleObjectException;

/**
 * Class EmailService
 *
 * @property EmailServiceHelper $helper
 */
class EmailService extends SendMail implements EmailServiceInterface
{
    private EmailServiceHelper $helper;

    public function __construct(EmailServiceHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     *
     * @param Email $email
     * @return array
     */
    protected function generateContentData(EmailInterface $email): array
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

    /**
     * @param Email $email
     * @param array $data
     * @return mixed|null
     */
    public function sendMail($email, array $data = [])
    {
        $contentData = $this->generateContentData($email);
        return $this->sendToCommunication($email, $contentData, $data);
    }

    /**
     * @param Email $email
     * @param array $requestData
     */
    public function updateAfterSendMail(Email $email, array $requestData)
    {
        if ($requestData && isset($requestData['eq_status_id'])) {
            $email->updateAttributes([
                'e_status_id' => $requestData['eq_status_id'],
                'e_type_id' =>  EmailType::isDraft($email->e_type_id) ? EmailType::OUTBOX : $email->e_type_id,
                'e_communication_id' =>  $requestData['eq_id']
            ]);
        }
    }

    /**
     * @param EmailForm $form
     * @return Email
     * @throws StaleObjectException
     * @throws CreateModelException
     */
    public function create(EmailForm $form): Email
    {
        $email = new Email();
        if ($form->emailId) {
            $email->e_id = $form->emailId;
        }
        $email->e_type_id = $form->type;
        $email->e_status_id = $form->status;
        $email->e_is_new = $form->log->isNew;
        $email->e_is_deleted = $form->isDeleted ?? 0;
        $email->e_reply_id = $form->replyId ?? null;
        $email->e_priority = $form->params->priority ?? null;
        $email->e_communication_id = $form->log->communicationId ?? null;
        $email->e_email_to = $form->contacts['to']->email;
        $email->e_email_to_name = $form->contacts['to']->name;
        $email->e_email_from = $form->contacts['from']->email;
        $email->e_email_from_name = $form->contacts['from']->name;
        if (isset($form->contacts['cc']) && !empty($form->contacts['cc']->emails)) {
            $email->e_email_cc = join(', ', $form->contacts['cc']->emails);
        }
        if (isset($form->contacts['bcc']) && !empty($form->contacts['bcc']->emails)) {
            $email->e_email_bc = join(', ', $form->contacts['bcc']->emails);
        }
        $email->e_email_subject = $form->body->subject;
        $email->e_project_id = $form->projectId;
        $email->body_html = $form->body->bodyHtml;
        $email->e_created_dt = $form->createdDt;
        $email->e_inbox_email_id = $form->log->inboxEmailId;
        $email->e_inbox_created_dt = $form->log->inboxCreatedDt;
        $email->e_ref_message_id = $form->log->refMessageId;
        $email->e_message_id = $form->log->messageId;
        $email->e_language_id = $form->params->language;
        $email->e_template_type_id = $form->params->templateType;
        $email->e_error_message = $form->log->errorMessage;
        $email->e_client_id = $form->clients ?? null;
        $email->e_lead_id = $form->leads ?? null;
        $email->e_case_id = $form->cases ?? null;
        $email->e_created_user_id = $form->getUserId() ?? null;
        $email->e_email_data = !empty($form->body->data) ? json_encode($form->body->data) : null;

        if (!$email->save()) {
            throw new CreateModelException(get_class($email), $email->getErrors());
        } elseif ($email->e_message_id == null) {
            $email->e_message_id = $email->generateMessageId();
            $email->update();
        }

        return $email;
    }

    /**
     *
     * @param Email $email
     * @param EmailForm $form
     * @throws RuntimeException
     * @return Email
     */
    public function update($email, EmailForm $form): Email
    {
        $email->e_type_id = $form->type;
        $email->e_status_id = $form->status;
        $email->e_is_new = $form->log->isNew;
        if ($form->isDeleted !== null) {
            $email->e_is_deleted = $form->isDeleted;
        }
        if (!empty($form->replyId)) {
            $email->e_reply_id = $form->replyId;
        }
        if ($form->log->errorMessage !== null) {
            $email->e_error_message = $form->log->errorMessage;
        }
        if ($form->params->priority !== null) {
            $email->e_priority = $form->params->priority;
        }
        if ($form->log->communicationId !== null) {
            $email->e_communication_id = $form->log->communicationId;
        }
        if ($form->log->statusDoneDt !== null) {
            $email->e_status_done_dt = $form->log->statusDoneDt;
        }
        if ($form->log->readDt !== null) {
            $email->e_read_dt = $form->log->readDt;
        }
        $email->e_email_to = $form->contacts['to']->email;
        $email->e_email_to_name = $form->contacts['to']->name;
        $email->e_email_from = $form->contacts['from']->email;
        $email->e_email_from_name = $form->contacts['from']->name;
        if (isset($form->contacts['cc'])) {
            $email->e_email_cc = !empty($form->contacts['cc']->emails) ? join(', ', $form->contacts['cc']->emails) : null;
        }
        if (isset($form->contacts['bcc'])) {
            $email->e_email_bc = !empty($form->contacts['bcc']->emails) ? join(', ', $form->contacts['bcc']->emails) : null;
        }
        $email->e_email_subject = $form->body->subject;
        $email->e_project_id = $form->projectId;
        $email->body_html = $form->body->bodyHtml;
        $email->e_updated_dt = $form->createdDt;
        $email->e_updated_user_id = $form->getUserId() ?? null;
        $email->e_inbox_email_id = $form->log->inboxEmailId;
        $email->e_inbox_created_dt = $form->log->inboxCreatedDt;
        $email->e_ref_message_id = $form->log->refMessageId;
        if ($form->log->messageId !== null) {
            $email->e_message_id = $form->log->messageId;
        }
        $email->e_language_id = $form->params->language;
        $email->e_template_type_id = $form->params->templateType;
        if ($form->clients !== null) {
            $email->e_client_id = $form->clients;
        }
        if ($form->leads !== null) {
            $email->e_lead_id = is_array($form->leads) ? $form->leads[0] : $form->leads;
        }
        if ($form->cases !== null) {
            $email->e_case_id = $form->cases;
        }
        $email->e_updated_user_id = $form->getUserId() ?? null;
        if (!empty($form->body->data)) {
            $email->e_email_data = json_encode($form->body->data);
        }

        if (!$email->save()) {
            throw new RuntimeException('Email save failed: ' . $email->getErrorSummary(true)[0]);
        }

        return $email;
    }

    /**
     * @param EmailPreviewFromInterface $previewEmailForm
     * @param array $attachments
     * @return Email
     */
    public function createFromPreviewForm(EmailPreviewFromInterface $previewEmailForm, array $attachments = []): Email
    {
        $mail = new Email();
        if ($previewEmailForm->getEmailTemplateId()) {
            $mail->e_template_type_id = $previewEmailForm->getEmailTemplateId();
        }
        $mail->e_type_id = EmailType::OUTBOX;
        $mail->e_status_id = EmailStatus::PENDING;
        $mail->e_email_subject = $previewEmailForm->getEmailSubject();
        $mail->body_html = $previewEmailForm->getEmailMessage();
        $mail->e_email_from = $previewEmailForm->getEmailFrom();
        $mail->e_email_from_name = $previewEmailForm->getEmailFromName();
        $mail->e_email_to_name = $previewEmailForm->getEmailToName();
        if ($previewEmailForm->getLanguageId()) {
            $mail->e_language_id = $previewEmailForm->getLanguageId();
        }
        $mail->e_email_to = $previewEmailForm->getEmailTo();
        $mail->e_created_dt = date('Y-m-d H:i:s');
        $mail->e_created_user_id = Yii::$app->user->id;
        $mail->e_email_data = !empty($attachments) ? json_encode($attachments) : null;

        return $mail;
    }

    /**
     * @param EmailPreviewFromInterface $previewEmailForm
     * @param Lead $lead
     * @param array $attachments
     * @return Email
     * @throws StaleObjectException
     * @throws CreateModelException
     */
    public function createFromLead(EmailPreviewFromInterface $previewEmailForm, Lead $lead, array $attachments = []): Email
    {
        $mail = $this->createFromPreviewForm($previewEmailForm, $attachments);
        $mail->e_project_id = $lead->project_id;
        $mail->e_lead_id = $lead->id;

        if ($mail->save()) {
            $mail->e_message_id = $mail->generateMessageId();
            $mail->update();
        } else {
            throw new CreateModelException(get_class($mail), $mail->getErrors());
        }
        return $mail;
    }

    /**
     * @param EmailPreviewFromInterface $previewEmailForm
     * @param Cases $case
     * @param array $attachments
     * @return Email
     * @throws StaleObjectException
     * @throws CreateModelException
     */
    public function createFromCase(EmailPreviewFromInterface $previewEmailForm, Cases $case, array $attachments = []): Email
    {
        $mail = $this->createFromPreviewForm($previewEmailForm, $attachments);
        $mail->e_project_id = $case->cs_project_id;
        $mail->e_case_id = $case->cs_id;

        if ($mail->save()) {
            $mail->e_message_id = $mail->generateMessageId();
            $mail->update();
        } else {
            throw new CreateModelException(get_class($mail), $mail->getErrors());
        }
        return $mail;
    }

    /**
     * @param EmailReviewQueueForm $form
     * @param $email
     * @return mixed
     * @throws Exception
     */
    public function updateAfterReview(EmailReviewQueueForm $form, $email)
    {
        $email->e_email_from = $form->emailFrom;
        $email->e_email_from_name = $form->emailFromName;
        $email->e_email_to = $form->emailTo;
        $email->e_email_to_name = $form->emailToName;
        $email->e_email_subject = $form->emailSubject;
        $email->e_status_id = EmailStatus::PENDING;
        $email->body_html = $form->emailMessage;

        if (!$email->save()) {
            throw new Exception($email->getErrorSummary(true)[0]);
        }

        return $email;
    }


    /**
     * @param EmailDTO $emailDTO
     * @param bool $autoDetectEmpty
     * @return Email
     * @throws StaleObjectException
     * @throws CreateModelException
     */
    public function createFromDTO(EmailDTO $emailDTO, bool $autoDetectEmpty = true): Email
    {
        $email = new Email();
        $email->e_type_id = $emailDTO->typeId;
        $email->e_status_id = $emailDTO->statusId;
        $email->e_is_new = $emailDTO->isNew;
        $email->e_email_to = $emailDTO->emailTo;
        $email->e_email_to_name = $emailDTO->emailToName;
        $email->e_email_from = $emailDTO->emailFrom;
        $email->e_email_from_name = $emailDTO->emailFromName;
        $email->e_email_cc = !empty($emailDTO->emailCc) ? $emailDTO->emailCc : null;
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
        $email->e_client_id = $emailDTO->clientId ?? null;
        $email->e_lead_id = $emailDTO->leadId ?? null;
        $email->e_case_id = $emailDTO->caseId ?? null;
        $email->e_created_user_id = $emailDTO->createdUserId ?? null;
        $email->e_email_data = !empty($emailDTO->attachments) ? json_encode($emailDTO->attachments) : null;
        if ($autoDetectEmpty) {
            $email->e_client_id = $emailDTO->clientId ?? $this->helper->detectClientId($emailDTO->emailFrom);
            $email->e_lead_id = $emailDTO->leadId ?? $this->helper->detectLeadId($emailDTO->emailSubject, $emailDTO->refMessageId);
            $email->e_case_id = $emailDTO->caseId ?? $this->helper->detectCaseId($emailDTO->emailSubject, $emailDTO->refMessageId);
            $email->e_created_user_id = $emailDTO->createdUserId ?? $this->helper->getUserIdByEmail($emailDTO->emailTo);
        }

        if (!$email->save()) {
            throw new CreateModelException(get_class($email), $email->getErrors());
        } elseif ($email->e_message_id == null) {
            $email->e_message_id = $email->generateMessageId();
            $email->update();
        }

        return $email;
    }

    /**
     * @param Email $email
     * @param int $statusId
     * @return void
     */
    public function changeStatus(Email $email, int $statusId): void
    {
        if (EmailStatus::isDone($statusId)) {
            $email->e_status_done_dt = date('Y-m-d H:i:s');
        }
        $email->e_status_id = $statusId;
        $email->save(false);
    }
}

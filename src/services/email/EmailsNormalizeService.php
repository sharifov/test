<?php

namespace src\services\email;

use common\models\Email as EmailOld;
use common\models\Lead;
use frontend\models\EmailPreviewFromInterface;
use src\auth\Auth;
use src\dto\email\EmailDTO;
use src\entities\cases\Cases;
use src\entities\email\Email;
use src\entities\email\EmailAddress;
use src\entities\email\EmailBlob;
use src\entities\email\EmailBody;
use src\entities\email\EmailContact;
use src\entities\email\EmailInterface;
use src\entities\email\form\EmailForm;
use src\entities\email\helpers\EmailContactType;
use src\entities\email\helpers\EmailStatus;
use src\entities\email\helpers\EmailType;
use src\forms\emailReviewQueue\EmailReviewQueueForm;
use src\helpers\email\TextConvertingHelper;
use Yii;

/**
 *
 * Class EmailsNormalizeService
 *
 * @property EmailServiceHelper $helper
 * @property int|null $userId
 *
 */
class EmailsNormalizeService extends SendMail implements EmailServiceInterface
{
    protected ?int $userId;
    private EmailServiceHelper $helper;

    public function __construct(EmailServiceHelper $helper)
    {
        $this->userId = Auth::id();
        $this->helper = $helper;
    }

    /**
     * @return EmailsNormalizeService
     * @throws \yii\base\InvalidConfigException
     */
    public static function newInstance(): EmailsNormalizeService
    {
        return new static(Yii::createObject(EmailServiceHelper::class));
    }

    /**
     * @param EmailOld $emailOld
     * @return array
     */
    public static function getDataArrayFromOld(EmailOld $emailOld): array
    {
        $data = [
            'userId'        =>  $emailOld->e_created_user_id,
            'emailId'       =>  $emailOld->e_id,
            'type'          =>  $emailOld->e_type_id,
            'status'        =>  $emailOld->e_status_id,
            'isDeleted'     =>  $emailOld->e_is_deleted,
            'projectId'     =>  $emailOld->e_project_id,
            'depId'         =>  $emailOld->departmentId,
            'createdDt'     =>  $emailOld->e_created_dt,
            'updatedDt'     =>  $emailOld->e_updated_dt,
            'clientsIds'    =>  $emailOld->e_client_id ? [$emailOld->e_client_id] : null,
            'casesIds'      =>  $emailOld->e_case_id ? [$emailOld->e_case_id] : null,
            'leadsIds'      =>  $emailOld->e_lead_id ? [$emailOld->e_lead_id] : null,
            'replyId'       =>  $emailOld->e_reply_id,
        ];

        $data['params'] = [
            'templateType'  =>  $emailOld->e_template_type_id,
            'language'      =>  $emailOld->e_language_id,
            'priority'      =>  $emailOld->e_priority,
        ];

        $data['body'] = [
            'subject'   =>  $emailOld->e_email_subject,
            'text'      =>  $emailOld->e_email_body_text,
            'bodyHtml'  =>  $emailOld->e_email_body_blob ? TextConvertingHelper::unCompress($emailOld->e_email_body_blob) : $emailOld->body_html,
            'data'      =>  !empty($emailOld->e_email_data) ? json_decode($emailOld->e_email_data, true) : null,
        ];

        $data['log'] = [
            'communicationId'   =>  $emailOld->e_communication_id,
            'messageId'         =>  $emailOld->e_message_id,
            'errorMessage'      =>  $emailOld->e_error_message,
            'statusDoneDt'      =>  $emailOld->e_status_done_dt,
            'readDt'            =>  $emailOld->e_read_dt,
            'refMessageId'      =>  $emailOld->e_ref_message_id,
            'inboxCreatedDt'    =>  $emailOld->e_inbox_created_dt,
            'inboxEmailId'      =>  $emailOld->e_inbox_email_id,
            'isNew'             =>  $emailOld->e_is_new,
        ];

        $data['contacts'] = [
            'from' => [
                'email' => $emailOld->e_email_from,
                'name'  =>  $emailOld->e_email_from_name,
                'type' => EmailContactType::FROM,
            ],
            'to' => [
                'email' => $emailOld->e_email_to,
                'name'  =>  $emailOld->e_email_to_name,
                'type' => EmailContactType::TO,
            ],
            'cc' => [
                'emails' => !empty($emailOld->e_email_cc) ? explode(', ', $emailOld->e_email_cc) : [],
                'type' => EmailContactType::CC,
            ],
            'bcc' => [
                'emails' => !empty($emailOld->e_email_bc) ? explode(', ', $emailOld->e_email_bc) : [],
                'type' => EmailContactType::BCC,
            ],
        ];

        return $data;
    }

    /**
     * @param EmailOld $emailOld
     * @return Email
     * @throws yii\db\Exception
     * @throws yii\db\StaleObjectException
     * @throws \Throwable
     */
    public function createEmailFromOld(EmailOld $emailOld): Email
    {
        $form = EmailForm::fromArray(self::getDataArrayFromOld($emailOld));

        return $this->create($form);
    }

    /**
     * @return bool|int|string|null
     */
    public static function getOldTotal()
    {
        return EmailOld::find()->count();
    }

    /**
     * @return int|null
     * @throws yii\db\Exception
     */
    public static function getTotalConnectedWithOld(): ?int
    {
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand(
            "SELECT COUNT(emo.e_id)
             FROM `" . Email::tableName() . "` emn
            INNER JOIN `" . EmailOld::tableName() . "` emo ON emo.e_id = emn.e_id"
        );
        return $command->queryScalar();
    }

    /**
     * @param EmailForm $form
     * @return Email
     * @throws yii\db\Exception
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function create(EmailForm $form): Email
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            //=Email
            $emailData = [
                'e_type_id' => $form->type,
                'e_status_id' => $form->status,
                'e_project_id' => $form->projectId,
                'e_departament_id' => $form->depId,
                'e_created_user_id' => $form->getUserId() ?? $this->userId,
                'e_id' => $form->emailId ?? null,
                'e_created_dt' => $form->createdDt ?? date('Y-m-d H:i:s'),
                'e_updated_dt' => $form->updatedDt ?? date('Y-m-d H:i:s'),
                'e_is_deleted' => $form->isDeleted ?? 0,
            ];
            /** @var Email $email */
            $email = Email::create($emailData);
            //=!Email

            //=EmailParams
            if (!$form->params->isEmpty()) {
                $email->saveParams($form->params->getAttributesForModel(true));
            }
            //=!EmailParams

            //=EmailLog
            if (!$form->log->isEmpty()) {
                $email->saveEmailLog($form->log->getAttributesForModel());
            }
            //=!EmailParams

            //=EmailBody
            $bodyText = $form->body->getText();
            $bodyData = [
                'embd_email_subject' => $form->body->subject,
                'embd_email_body_text' => $bodyText,
                'embd_email_data' => $form->body->data,
                'embd_hash' => hash('crc32b', join(' | ', [$form->body->subject, $bodyText])),
            ];
            $emailBody = EmailBody::create($bodyData);
            $email->link('emailBody', $emailBody);
            //=!EmailBody

            //=EmailBlob
            EmailBlob::create([
                'embb_email_body_blob' => $form->body->getBodyHtml(),
                'embb_body_id' => $emailBody->embd_id
            ]);
            //=!EmailBlob

            //=EmailContacts
            foreach ($form->contacts as $contactForm) {
                if (!empty($contactForm->email)) {
                    $email->addContact($contactForm->type, $contactForm->email, $contactForm->name);
                } elseif (!empty($contactForm->emails) && is_array($contactForm->emails)) {
                    foreach ($contactForm->emails as $contEmail) {
                        $email->addContact($contactForm->type, $contEmail);
                    }
                }
            }
            //=!EmailContacts

            $email->refresh();

            //=link Clients
            $clientsIds = $form->clients ?? [$this->helper->detectClientId($email->getEmailTo(false))];
            if (!empty($clientsIds)) {
                $email->linkClients($clientsIds);
            }
            //=!link Clients

            //=link Cases
            if (!empty($form->cases)) {
                $email->linkCases($form->cases);
            }
            //=!link Cases

            //=link Leads
            if (!empty($form->leads)) {
                $email->linkLeads($form->leads);
            }
            //=!link Leads

            //=link Reply
            if (!empty($form->replyId)) {
                $email->linkReply($form->replyId);
            }
            //=!link Reply

            if (null === $email->getMessageId()) {
                $email->setMessageId();
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $email;
    }

    /**
     * @param $email
     * @param EmailForm $form
     * @return Email
     * @throws yii\db\Exception
     * @throws \Throwable
     */
    public function update($email, EmailForm $form): Email
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            //=update Email
            $email->attributes = $form->getAttributesForModel(true); //->toArray();
            $email->e_updated_user_id = $form->getUserId() ?? $this->userId;
            unset($email->e_created_user_id);
            $email->save();
            //=!update Email

            //=EmailParams
            if (!$form->params->isEmpty()) {
                $email->saveParams($form->params->getAttributesForModel(true));
            }
            //=!EmailParams

            //=EmailLog
            if (!$form->log->isEmpty()) {
                $email->saveEmailLog($form->log->getAttributesForModel(true));
            }
            //=!EmailParams

            //=EmailBody
            $bodyText = $form->body->getText();
            $emailBody = $email->emailBody;
            $emailBodyAttr = [
                'embd_email_subject' => $form->body->subject,
                'embd_email_body_text' => $bodyText,
                'embd_hash' => hash('crc32b', join(' | ', [$form->body->subject, $bodyText])),
            ];
            if ($form->body->data !== null) {
                $emailBodyAttr['embd_email_data'] = $form->body->data;
            }
            $emailBody->attributes = $emailBodyAttr;
            $emailBody->save();
            //=!EmailBody

            //=EmailBlob
            $emailBlob = $emailBody->emailBlob;
            $emailBlob->updateAttributes(['embb_email_body_blob' => $form->body->getBodyHtml()]);
            //=!EmailBlob

            //=EmailContacts
            foreach ($form->contacts as $contactForm) {
                if (EmailContactType::isRequired($contactForm->type)) { // TO OR FROM
                    if (isset($contactForm->id) && !empty($contactForm->id)) {
                        $contact = EmailContact::findOne($contactForm->id);
                    } else {
                        $contact = EmailContact::findOne([
                            'ec_email_id' => $email->e_id,
                            'ec_type_id' => $contactForm->type
                        ]);
                    }

                    if ($contact && isset($contactForm->email) && !empty($contactForm->email)) {
                        $address = EmailAddress::findOrNew($contactForm->email, $contactForm->name, !empty($contactForm->name));
                        $contact->updateAttributes([
                            'ec_address_id' => $address->ea_id,
                        ]);
                    }
                } else {
                    $emailContacts = $email->getEmailsByType($contactForm->type);
                    $remove = array_diff($emailContacts, $contactForm->emails ?? []);

                    if ($contactForm->emails) {
                        foreach ($contactForm->emails as $contEmail) {
                            $email->addContact($contactForm->type, $contEmail);
                        }
                    }
                    foreach ($remove as $remEmail) {
                        $email->removeContact($contactForm->type, $remEmail);
                    }
                }
            }
            //=!EmailContacts

            //=link Clients
            $clientsIds = $form->clients;
            if (!empty($clientsIds)) {
                $email->linkClients($clientsIds);
            }
            //=!link Clients

            //=link Cases
            if (!empty($form->cases)) {
                $email->linkCases($form->cases);
            }
            //=!link Cases

            //=link Leads
            if (!empty($form->leads)) {
                $email->linkLeads($form->leads);
            }
            //=!link Leads

            //=link Reply
            if (!empty($form->replyId)) {
                $email->linkReply($form->replyId);
            }
            //=!link Reply

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $email;
    }

    /**
     *
     * @param Email $email
     * @return array
     */
    protected function generateContentData(EmailInterface $email): array
    {
        $content_data['email_body_html'] = $email->emailBody->getBodyHtml();
        $content_data['email_body_text'] = $email->emailBody->embd_email_body_text;
        $content_data['email_subject'] = $email->emailBody->embd_email_subject;
        $content_data['email_reply_to'] = $email->emailFrom;
        $content_data['email_cc'] = !empty($email->emailsCc) ? join(', ', $email->emailsCc) : null;
        $content_data['email_bcc'] = !empty($email->emailsBcc) ? join(', ', $email->emailsBcc) : null;
        if ($email->contactFrom->ea_name) {
            $content_data['email_from_name'] = $email->contactFrom->ea_name;
        }
        if ($email->contactTo->ea_name) {
            $content_data['email_to_name'] = $email->contactTo->ea_name;
        }
        if ($email->emailLog && $email->emailLog->el_message_id) {
            $content_data['email_message_id'] = $email->emailLog->el_message_id;
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
     * @param $requestData
     * @return void
     */
    public function updateAfterSendMail(Email $email, $requestData)
    {
        if ($requestData && isset($requestData['eq_status_id'])) {
            $email->updateAttributes([
                'e_status_id' => $requestData['eq_status_id'],
                'e_type_id' =>  EmailType::isDraft($email->e_type_id) ? EmailType::OUTBOX : $email->e_type_id
            ]);
            $email->saveEmailLog(['el_communication_id' => $requestData['eq_id']]);
        }
    }

    /**
     * @param EmailPreviewFromInterface $previewEmailForm
     * @param array $attachments
     * @return array
     */
    public function getDataArrayFromPreviewForm(EmailPreviewFromInterface $previewEmailForm, array $attachments = []): array
    {
        return [
            'userId'        =>  $this->userId,
            'type'          =>  EmailType::OUTBOX,
            'status'        =>  EmailStatus::PENDING,
            'createdDt'     =>  date('Y-m-d H:i:s'),
            'params'        => [
                'templateType'  =>  $previewEmailForm->getEmailTemplateId(),
                'language'      =>  $previewEmailForm->getLanguageId() ?? null,
            ],
            'body'          =>  [
                'subject'   =>  $previewEmailForm->getEmailSubject(),
                'bodyHtml'  =>  $previewEmailForm->getEmailMessage(),
                'data'      =>  !empty($attachments) ? $attachments : null,
            ],
            'contacts'      =>  [
                'from' => [
                    'email' => $previewEmailForm->getEmailFrom(),
                    'name'  =>  $previewEmailForm->getEmailFromName(),
                    'type' => EmailContactType::FROM,
                ],
                'to' => [
                    'email' => $previewEmailForm->getEmailTo(),
                    'name'  =>  $previewEmailForm->getEmailToName(),
                    'type' => EmailContactType::TO,
                ],
            ]
        ];
    }

    /**
     * @param EmailPreviewFromInterface $previewEmailForm
     * @param Lead $lead
     * @param array $attachments
     * @param int|null $emailId
     * @return Email
     * @throws \Throwable
     * @throws yii\db\Exception
     */
    public function createFromLead(EmailPreviewFromInterface $previewEmailForm, Lead $lead, array $attachments = [], ?int $emailId = null): Email
    {
        $data = $this->getDataArrayFromPreviewForm($previewEmailForm, $attachments);
        $data['projectId'] = $lead->project_id;
        $data['depId'] = $lead->l_dep_id;
        $data['leadsIds'] = [$lead->id];
        $data['emailId'] = $emailId;

        return $this->create(EmailForm::fromArray($data));
    }

    /**
     * @param EmailPreviewFromInterface $previewEmailForm
     * @param Cases $case
     * @param array $attachments
     * @param int|null $emailId
     * @return Email
     * @throws \Throwable
     * @throws yii\db\Exception
     */
    public function createFromCase(EmailPreviewFromInterface $previewEmailForm, Cases $case, array $attachments = [], ?int $emailId = null): Email
    {
        $data = $this->getDataArrayFromPreviewForm($previewEmailForm, $attachments);
        $data['projectId'] = $case->cs_project_id;
        $data['depId'] = $case->cs_dep_id;
        $data['casesIds'] = [$case->cs_id];
        $data['emailId'] = $emailId;

        return $this->create(EmailForm::fromArray($data));
    }


    /**
     * @param EmailReviewQueueForm $form
     * @param Email $email
     * @return Email
     * @throws yii\db\Exception
     * @throws \Throwable
     */
    public function updateAfterReview(EmailReviewQueueForm $form, $email): Email
    {
        $data = [
            'userId'        =>  $this->userId,
            'status'        =>  EmailStatus::PENDING,
            'body'  =>  [
                'subject'   =>  $form->emailSubject,
                'bodyHtml'  =>  $form->emailMessage,
            ],
            'contacts' => [
                'from' => [
                    'email' => $form->emailFrom,
                    'name'  =>  $form->emailFromName,
                    'type' => EmailContactType::FROM,
                ],
                'to' => [
                    'email' => $form->emailTo,
                    'name'  =>  $form->emailToName,
                    'type' => EmailContactType::TO,
                ],
            ]
        ];
        return $this->update($email, EmailForm::fromArray($data));
    }

    /**
     * @param EmailDTO $emailDTO
     * @param bool $autoDetectEmpty
     * @return Email
     * @throws yii\db\Exception
     * @throws \Throwable
     * @throws yii\db\StaleObjectException
     */
    public function createFromDTO(EmailDTO $emailDTO, bool $autoDetectEmpty = true): Email
    {
        $clientId = $emailDTO->clientId ?? null;
        $leadId = $emailDTO->leadId ?? null;
        $caseId = $emailDTO->caseId ?? null;
        if ($emailDTO->createdUserId) {
            $this->userId = $emailDTO->createdUserId;
        }
        if ($autoDetectEmpty) {
            $clientId = $emailDTO->clientId ?? $this->helper->detectClientId($emailDTO->emailFrom);
            $leadId =  $emailDTO->leadId ?? $this->helper->detectLeadId($emailDTO->emailSubject, $emailDTO->refMessageId);
            $caseId = $emailDTO->caseId ?? $this->helper->detectCaseId($emailDTO->emailSubject, $emailDTO->refMessageId);
            $this->userId = $emailDTO->createdUserId ?? $this->helper->getUserIdByEmail($emailDTO->emailTo);
        }

        $data = [
            'emailId'       =>  $emailDTO->emailId ?? null,
            'userId'        =>  $this->userId,
            'status'        =>  $emailDTO->statusId,
            'type'          =>  $emailDTO->typeId,
            'projectId'     =>  $emailDTO->projectId ?? $this->helper->getProjectIdByDepOrUpp($emailDTO->emailTo),
            'clientsIds'    =>  $clientId ? [$clientId] : null,
            'body'  =>  [
                'subject'   =>  $emailDTO->emailSubject,
                'bodyHtml'  =>  $emailDTO->bodyHtml,
                'data'      =>  !empty($emailDTO->attachments) ? $emailDTO->attachments : null,
            ],
            'contacts' => [
                'from' => [
                    'email' => $emailDTO->emailFrom,
                    'name'  =>  $emailDTO->emailFromName,
                    'type' => EmailContactType::FROM,
                ],
                'to' => [
                    'email' => $emailDTO->emailTo,
                    'name'  =>  $emailDTO->emailToName,
                    'type' => EmailContactType::TO,
                ],
            ],
            'log'   =>  [
                'messageId'         =>  $emailDTO->messageId,
                'refMessageId'      =>  $emailDTO->refMessageId,
                'inboxCreatedDt'    =>  $emailDTO->inboxCreatedDt,
                'inboxEmailId'      =>  $emailDTO->inboxEmailId,
                'isNew'             =>  $emailDTO->isNew !== null ? intval($emailDTO->isNew) : null,
            ]
        ];

        if (!empty($emailDTO->templateTypeId) || !empty($emailDTO->languageId)) {
            $data['params'] = [
                'templateType'  =>  $emailDTO->templateTypeId,
                'language'      =>  $emailDTO->languageId,
            ];
        }

        $data['leadsIds'] = $leadId ? [$leadId] : null;
        $data['casesIds'] = $caseId ? [$caseId] : null;

        return $this->create(EmailForm::fromArray($data));
    }

    /**
     * @param Email $email
     * @param int $statusId
     * @return void
     */
    public function changeStatus(Email $email, int $statusId): void
    {
        if (EmailStatus::isDone($statusId)) {
            $email->saveEmailLog(['el_status_done_dt' => date('Y-m-d H:i:s')]);
        }
        $email->e_status_id = $statusId;
        $email->save(false);
    }
}

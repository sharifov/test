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
use src\entities\email\EmailLog;
use src\entities\email\EmailRepository;
use src\entities\email\form\EmailForm;
use src\entities\email\helpers\EmailContactType;
use src\entities\email\helpers\EmailStatus;
use src\entities\email\helpers\EmailType;
use src\forms\emailReviewQueue\EmailReviewQueueForm;
use src\helpers\email\TextConvertingHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 *
 * Class EmailsNormalizeService
 *
 * @property EmailServiceHelper $helper
 * @property EmailRepository $emailRepository
 *
 */
class EmailsNormalizeService extends SendMail implements EmailServiceInterface
{
    protected $userId;
    /**
     * @var EmailServiceHelper
     */
    private $helper;
    /**
     * @var EmailRepository
     */
    private $emailRepository;


    public static function newInstance()
    {
        $instance = new static();
        $instance->userId = Auth::id();
        $instance->helper = Yii::createObject(EmailServiceHelper::class);
        $instance->emailRepository = Yii::createObject(EmailRepository::class);
        return $instance;
    }

    public function getDataArrayFromOld(EmailOld $emailOld)
    {
        $data = [
            'userId'        =>  $emailOld->e_created_user_id,
            'emailId'       =>  $emailOld->e_id,
            'type'          =>  $emailOld->e_type_id,
            'status'        =>  $emailOld->e_status_id,
            'isDeleted'     =>  $emailOld->e_is_deleted,
            'projectId'     =>  $emailOld->e_project_id,
            'depId'         =>  $emailOld->eLead ? $emailOld->eLead->l_dep_id : null,
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
            'bodyHtml'  =>  TextConvertingHelper::unCompress($emailOld->e_email_body_blob),
            'data'      =>  $emailOld->e_email_data,
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
                'email' => $emailOld->e_email_cc,
                'type' => EmailContactType::CC,
            ],
            'bcc' => [
                'email' => $emailOld->e_email_bc,
                'type' => EmailContactType::BCC,
            ],
        ];

        return $data;
    }

    public function createEmailFromOld(EmailOld $emailOld)
    {
        $form = EmailForm::fromArray($this->getDataArrayFromOld($emailOld));

        return $this->create($form);
    }

    public function getAddress(string $email, ?string $name, $update = false): EmailAddress
    {
        $attributes = [
            'ea_email' => $email,
            'ea_name' => preg_replace('~\"(.*)\"~iU', "$1", $name),
        ];
        $address = EmailAddress::findOneOrNew(['ea_email' => $email]);
        if ($address->isNewRecord || $update) {
            $address->attributes = $attributes;
            $address->save();
        }

        return $address;
    }

    public static function getOldTotal()
    {
        return EmailOld::find()->count();
    }

    public static function getTotalConnectedWithOld()
    {
        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand(
            "SELECT COUNT(emo.e_id)
             FROM `".Email::tableName()."` emn
            INNER JOIN `".EmailOld::tableName()."` emo ON emo.e_id = emn.e_id"
            );
        return $command->queryScalar();
    }

    public function create(EmailForm $form)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            //=Email
            $emailData = [
                'e_type_id' => $form->type,
                'e_status_id' => $form->status,
                'e_project_id' => $form->projectId,
                'e_departament_id' => $form->depId,
                'e_created_user_id' => $form->getUserId() ?? $this->userId,
            ];
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
            foreach ($form->contacts as $contactForm)
            {
                $address = $this->getAddress($contactForm->email, $contactForm->name, true);
                EmailContact::create([
                    'ec_address_id' => $address->ea_id,
                    'ec_email_id' => $email->e_id,
                    'ec_type_id' => $contactForm->type
                ]);
            }
            //=!EmailContacts

            $email->refresh();

            //=link Clients
            $clientsIds = $form->clients ?? [$this->helper->detectClientId($email->emailTo)];
            if (!empty($clientsIds)) {
                $this->emailRepository->linkClients($email, $clientsIds);

            }
            //=!link Clients

            //=link Cases
            if (!empty($form->cases)) {
                $this->emailRepository->linkCases($email, $form->cases);
            }
            //=!link Cases

            //=link Leads
            if (!empty($form->leads)) {
                $this->emailRepository->linkLeads($email, $form->leads);
            }
            //=!link Leads

            //=link Reply
            if ($form->replyId !== null) {
                $this->emailRepository->linkReply($email, $form->replyId);
            }
            //=!link Reply

            if ($email->getMessageId() === null) {
                $email->setMessageId();
            }

            $transaction->commit();

        }catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $email;
    }

    public function update(Email $email, EmailForm $form)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            //=update Email
            $email->attributes = $form->getAttributesForModel(true); //->toArray();
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
            $emailBody->attributes = [
                'embd_email_subject' => $form->body->subject,
                'embd_email_body_text' => $bodyText,
                'embd_email_data' => $form->body->data,
                'embd_hash' => hash('crc32b', join(' | ', [$form->body->subject, $bodyText])),
            ];
            $emailBody->save();
            //=!EmailBody

            //=EmailBlob
            $emailBlob = $emailBody->emailBlob;
            $emailBlob->updateAttributes(['embb_email_body_blob' => $form->body->getBodyHtml()]);
            //=!EmailBlob

            //=EmailContacts
            $contactsByType = ArrayHelper::index($email->emailContacts, 'ec_type_id');
            $contactsById = ArrayHelper::index($email->emailContacts, 'ec_id');
            foreach ($form->contacts as $contactForm)
            {
                if (isset($contactForm->id)) {
                    $contact = $contactsById[$contactForm->id];
                } else {
                    $contact = $contactsByType[$contactForm->type];
                }

                if ($contact) {
                    $address = $this->getAddress($contactForm->email, $contactForm->name, true);

                    $contact->updateAttributes( [
                        'ec_address_id' => $address->ea_id,
                        'ec_type_id' => $contactForm->type
                    ]);
                }
            }
            //=!EmailContacts

            //=link Clients
            $clientsIds = $form->clients;
            if (!empty($clientsIds)) {
                $this->emailRepository->linkClients($email, $clientsIds);

            }
            //=!link Clients

            //=link Cases
            if (!empty($form->cases)) {
                $this->emailRepository->linkCases($email, $form->cases);
            }
            //=!link Cases

            //=link Leads
            if (!empty($form->leads)) {
                $this->emailRepository->linkLeads($email, $form->leads);
            }
            //=!link Leads

            //=link Reply
            if ($form->replyId !== null) {
                $this->emailRepository->linkReply($email, $form->replyId);
            }
            //=!link Reply

            $transaction->commit();

        }catch (\Throwable $e) {
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
    protected function generateContentData(EmailInterface $email)
    {
        $content_data['email_body_html'] = $email->emailBody->getBodyHtml();
        $content_data['email_body_text'] = $email->emailBody->embd_email_body_text;
        $content_data['email_subject'] = $email->emailBody->embd_email_subject;
        $content_data['email_reply_to'] = $email->emailFrom;
        //TODO: to write cc bcc logic
        //$content_data['email_cc'] = $this->e_email_cc;
        //$content_data['email_bcc'] = $this->e_email_bc;
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

    public function sendMail(EmailInterface $email, array $data = [])
    {
        $contentData = $this->generateContentData($email);
        return $this->sendToCommunication($email, $contentData, $data);
    }

    public function updataAfterSendMail(Email $email, $requestData)
    {
        if ($requestData && isset($requestData['eq_status_id'])) {
            $email->updateAttributes([
                'e_status_id' => $requestData['eq_status_id'],
                'e_type_id' =>  EmailType::isDraft($email->e_type_id) ? EmailType::OUTBOX : $email->e_type_id
            ]);
            $email->saveEmailLog(['el_communication_id' => $requestData['eq_id']]);
        }
    }

    public function getDataArrayFromPreviewForm(EmailPreviewFromInterface $previewEmailForm, array $attachments = [])
    {
        $data = [
            'userId'        =>  $this->userId,
            'type'          =>  EmailType::OUTBOX,
            'status'        =>  EmailStatus::PENDING,
            'createdDt'     =>  date('Y-m-d H:i:s'),
            'params'        => [
                'templateType'  =>  $previewEmailForm->getEmailTemplateId() ?? null,
                'language'      =>  $previewEmailForm->getLanguageId() ?? null,
            ],
            'body'          =>  [
                'subject'   =>  $previewEmailForm->getEmailSubject(),
                'bodyHtml'  =>  $previewEmailForm->getEmailMessage(),
                'data'      =>  $attachments,
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

        return $data;
    }

    public function createFromLead(EmailPreviewFromInterface $previewEmailForm, Lead $lead, array $attachments = []): Email
    {
        $data = $this->getDataArrayFromPreviewForm($previewEmailForm, $attachments);
        $data['projectId'] = $lead->project_id;
        $data['depId'] = $lead->l_dep_id;
        $data['leadsIds'] = [$lead->id];

        return $this->create(EmailForm::fromArray($data));
    }

    public function createFromCase(EmailPreviewFromInterface $previewEmailForm, Cases $case, array $attachments = []): Email
    {
        $data = $this->getDataArrayFromPreviewForm($previewEmailForm, $attachments);
        $data['projectId'] = $case->cs_project_id;
        $data['depId'] = $case->cs_dep_id;
        $data['casesIds'] = [$case->cs_id];

        return $this->create(EmailForm::fromArray($data));
    }

    public function updateAfterReview(EmailReviewQueueForm $form, $email)
    {
        try {
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
            $email = $this->update($email, EmailForm::fromArray($data));
        } catch (\Throwable $e) {
            throw $e;
        }

        return $email;
    }

    public function createFromDTO(EmailDTO $emailDTO, $autoDetectEmpty = true): Email
    {
        if ($autoDetectEmpty) {
            $clientId = $emailDTO->clientId ?? $this->helper->detectClientId($emailDTO->emailFrom);
            $leadId =  $emailDTO->leadId ?? $this->helper->detectLeadId($emailDTO->emailSubject, $emailDTO->refMessageId);
            $caseId = $emailDTO->caseId ?? $this->helper->detectCaseId($emailDTO->emailSubject, $emailDTO->refMessageId);
            $this->userId = $emailDTO->createdUserId ??$this->helper->getUserIdByEmail($emailDTO->emailTo);
        }

        $data = [
            'userId'        =>  $this->userId,
            'status'        =>  $emailDTO->statusId,
            'type'          =>  $emailDTO->typeId,
            'projectId'     =>  $emailDTO->projectId ?? $this->helper->getProjectIdByDepOrUpp($emailDTO->emailTo),
            'clientsIds'    =>  $clientId ? [$clientId] : null,
            'body'  =>  [
                'subject'   =>  $emailDTO->emailSubject,
                'bodyHtml'  =>  $emailDTO->bodyHtml,
                'data'      =>  $emailDTO->attachments,
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
                'isNew'             =>  $emailDTO->isNew,
            ]
        ];

        if (!empty($emailDTO->templateTypeId) || !empty($emailDTO->languageId)) {
            $data['params'] = [
                'templateType'  =>  $emailDTO->templateTypeId,
                'language'      =>  $emailDTO->languageId,
            ];
        }

        $data['leadsIds'] = $leadId ? [$leadId]: null;
        $data['casesIds'] = $caseId ? [$caseId]: null;

        return $this->create(EmailForm::fromArray($data));
    }
}

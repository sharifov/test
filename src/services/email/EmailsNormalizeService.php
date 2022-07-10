<?php

namespace src\services\email;

use common\components\CommunicationService;
use common\models\Client;
use common\models\Email as EmailOld;
use common\models\Lead;
use modules\featureFlag\FFlag;
use src\auth\Auth;
use src\entities\cases\Cases;
use src\entities\email\Email;
use src\entities\email\EmailAddress;
use src\entities\email\EmailBlob;
use src\entities\email\EmailBody;
use src\entities\email\EmailContact;
use src\entities\email\EmailLog;
use src\entities\email\form\EmailCreateForm;
use src\entities\email\helpers\EmailContactType;
use src\entities\email\helpers\EmailStatus;
use src\entities\email\helpers\EmailType;
use src\helpers\app\AppHelper;
use src\helpers\email\TextConvertingHelper;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadUserData\entity\LeadUserData;
use src\model\leadUserData\entity\LeadUserDataDictionary;
use src\model\leadUserData\repository\LeadUserDataRepository;
use src\services\abtesting\email\EmailTemplateOfferABTestingService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use frontend\models\LeadPreviewEmailForm;
use src\exception\EmailNotSentException;
use common\models\ClientEmail;
use frontend\models\CasePreviewEmailForm;

/**
 *
 * Class EmailsNormalizeService
 *
 */
class EmailsNormalizeService implements EmailServiceInterface
{

    public static function newInstance()
    {
        return new static();
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
            'depId'         =>  null, //TODO: get from
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
        $form = EmailCreateForm::fromArray($this->getDataArrayFromOld($emailOld));

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

    public static function detectClientId(string $email): ?int
    {
        $clientEmail = ClientEmail::find()->byEmail($email)->one();

        return $clientEmail->client_id ?? null;
    }

    public function create(EmailCreateForm $form)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            //=Email
            $emailData = [
                'e_type_id' => $form->type,
                'e_status_id' => $form->status,
                'e_project_id' => $form->projectId,
                'e_departament_id' => $form->depId,
                'e_created_user_id' => $form->getUserId() ?? Auth::id(),
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
                $email->saveLog($form->log->getAttributesForModel());
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
            $clientsIds = $form->clients ?? [self::detectClientId($email->emailTo)];
            if (!empty($clientsIds)) {
                foreach ($clientsIds as $id) {
                    if ($client = Client::findOne($id)) {
                        $email->link('clients', $client);
                    }
                }

            }
            //=!link Clients

            //=link Cases
            if (!empty($form->cases)) {
                foreach ($form->cases as $id) {
                    if ($case = Cases::findOne($id)) {
                        $email->link('cases', $case);
                    }
                }
            }
            //=!link Cases

            //=link Leads
            if (!empty($form->leads)) {
                foreach ($form->leads as $id) {
                    if ($lead = Lead::findOne($id)) {
                        $email->link('leads', $lead);
                    }
                }
            }
            //=!link Leads

            //=link Reply
            if ($form->replyId !== null) {
                $reply = Email::findOne($replyId);
                if ($reply) {
                    $email->link('reply', $reply);
                }
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

    /**
     *
     * @param Email $email
     * @param array $data
     * @throws \RuntimeException|EmailNotSentException
     */
    public function sendMail($email, array $data = [])
    {
        /** @var CommunicationService $communication */
        $communication = Yii::$app->communication;
        $data['project_id'] = $email->e_project_id;

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

        $tplType = $email->templateType ? $email->templateType->etp_key : null;

        try {
            $language = $email->params->ep_language_id ?? 'en-US';
            if (is_null($email->params)) {
                $email->saveParams(['ep_language_id' => $language]);
            }
            $request = $communication->mailSend($email->e_project_id, $tplType, $email->emailFrom, $email->contactTo->ea_email, $content_data, $data, $language, 0);

            if ($request && isset($request['data']['eq_status_id'])) {
                $email->e_status_id = $request['data']['eq_status_id'];
                $email->e_type_id = EmailType::isDraft($email->e_type_id) ? EmailType::OUTBOX : $email->e_type_id;
                $email->saveEmailLog(['el_communication_id' => $request['data']['eq_id']]);
                $email->save();
            }

            if ($request && isset($request['error']) && $request['error']) {
                $errorData = @json_decode($request['error'], true);
                $errorMessage = $errorData['message'] ?: $request['error'];
                throw new EmailNotSentException($email->contactTo->ea_email, $errorMessage);
            }
            /** @fflag FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES, A/B testing for email offer templates enable/disable */
            if (EmailStatus::notError($email->e_status_id) && Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_A_B_TESTING_EMAIL_OFFER_TEMPLATES)) {
                if ($email->e_template_type_id && $email->e_project_id && isset($email->lead)) {
                    EmailTemplateOfferABTestingService::incrementCounterByTemplateAndProjectIds(
                        $email->e_template_type_id,
                        $email->e_project_id,
                        $email->e_departament_id
                        );
                }
            }
            if ($email->e_id && $email->lead && LeadPoorProcessingService::checkEmailTemplate($tplType)) {
                LeadPoorProcessingService::addLeadPoorProcessingRemoverJob(
                    $email->lead->id,
                    [
                        LeadPoorProcessingDataDictionary::KEY_NO_ACTION,
                        LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE,
                        LeadPoorProcessingDataDictionary::KEY_SEND_SMS_OFFER,
                    ],
                    LeadPoorProcessingLogStatus::REASON_EMAIL
                    );

                if (($lead = $email->lead) && $lead->employee_id && $lead->isProcessing()) {
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
                        \Yii::warning($message, 'EmailsNormalizeService:LeadUserData:Exception');
                    } catch (\Throwable $throwable) {
                        $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['emailId' => $email->e_id]);
                        \Yii::error($message, 'EmailsNormalizeService:LeadUserData:Throwable');
                    }
                }
            }
        } catch (\Throwable $exception) {
            $error = VarDumper::dumpAsString($exception->getMessage());
            \Yii::error($error, 'EmailsNormalizeService:sendMail:mailSend:exception');
            $email->statusToError('Communication error: ' . $error);
            throw new \RuntimeException($error);
        }
    }

    public function createFromLead(LeadPreviewEmailForm $previewEmailForm, Lead $lead, array $attachments = []): Email
    {
        $data = [
            'userId'        =>  Yii::$app->user->id,
            'type'          =>  EmailType::OUTBOX,
            'status'        =>  EmailStatus::PENDING,
            'projectId'     =>  $lead->project_id,
            'depId'         =>  $lead->l_dep_id,
            'createdDt'     =>  date('Y-m-d H:i:s'),
            'leadsIds'      =>  [$lead->id],
        ];

        $data['params'] = [
            'templateType'  =>  $previewEmailForm->e_email_tpl_id ?? null,
            'language'      =>  $previewEmailForm->e_language_id ?? null,
        ];

        $data['body'] = [
            'subject'   =>  $previewEmailForm->e_email_subject,
            'bodyHtml'  =>  $previewEmailForm->e_email_message,
            'data'      =>  json_encode($attachments),
        ];

        $data['contacts'] = [
            'from' => [
                'email' => $previewEmailForm->e_email_from,
                'name'  =>  $previewEmailForm->e_email_from_name,
                'type' => EmailContactType::FROM,
            ],
            'to' => [
                'email' => $previewEmailForm->e_email_to,
                'name'  =>  $previewEmailForm->e_email_to_name,
                'type' => EmailContactType::TO,
            ],
        ];

        return $this->create(EmailCreateForm::fromArray($data));
    }

    public function createFromCase(CasePreviewEmailForm $previewEmailForm, Cases $case, array $attachments = []): Email
    {
        $data = [
            'userId'        =>  Yii::$app->user->id,
            'type'          =>  EmailType::OUTBOX,
            'status'        =>  EmailStatus::PENDING,
            'projectId'     =>  $case->cs_project_id,
            'depId'         =>  $case->cs_dep_id,
            'createdDt'     =>  date('Y-m-d H:i:s'),
            'casesIds'      =>  [$case->cs_id],
        ];

        $data['params'] = [
            'templateType'  =>  $previewEmailForm->e_email_tpl_id ?? null,
            'language'      =>  $previewEmailForm->e_language_id ?? null,
        ];

        $data['body'] = [
            'subject'   =>  $previewEmailForm->e_email_subject,
            'bodyHtml'  =>  $previewEmailForm->e_email_message,
            'data'      =>  json_encode($attachments),
        ];

        $data['contacts'] = [
            'from' => [
                'email' => $previewEmailForm->e_email_from,
                'name'  =>  $previewEmailForm->e_email_from_name,
                'type' => EmailContactType::FROM,
            ],
            'to' => [
                'email' => $previewEmailForm->e_email_to,
                'name'  =>  $previewEmailForm->e_email_to_name,
                'type' => EmailContactType::TO,
            ],
        ];

        return $this->create(EmailCreateForm::fromArray($data));
    }
}

<?php

namespace src\services\email;

use src\entities\email\EmailAddress;
use src\entities\email\EmailBody;
use src\entities\email\EmailBlob;
use src\helpers\email\TextConvertingHelper;
use src\entities\email\EmailParams;
use src\entities\email\EmailContact;
use src\entities\email\Email;
use common\models\Client;
use common\models\Email as EmailOld;
use src\entities\cases\Cases;
use common\models\Lead;
use src\entities\email\EmailLog;
use src\entities\email\form\EmailCreateForm;
use src\auth\Auth;
use common\components\CommunicationService;
use yii\helpers\VarDumper;
use src\entities\email\helpers\EmailStatus;
use modules\featureFlag\FFlag;
use src\services\abtesting\email\EmailTemplateOfferABTestingService;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadUserData\entity\LeadUserData;
use src\model\leadUserData\entity\LeadUserDataDictionary;
use src\model\leadUserData\repository\LeadUserDataRepository;
use yii\helpers\ArrayHelper;
use src\helpers\app\AppHelper;
use Yii;
use src\entities\email\helpers\EmailType;

/**
 *
 * Class EmailsNormalizeService
 *
 * @property Email $email
 * @property EmailLog $emailLog
 * @property EmailParams $emailParams
 * @property EmailBody $emailBody
 * @property EmailBlob $emailBlob
 * @property EmailContact[] $emailContacts
 * @property bool $isNew
 *
 */
class EmailsNormalizeService
{
    public $isNew;
    public $emailBody;
    public $emailBlob;
    public $emailContacts;
    public $email;
    public $emailLog;
    public $emailParams;
    public $errors = [];

    public static function newInstance()
    {
        return new static();
    }

    public function createEmailFromOld(EmailOld $emailOld)
    {
        $this->email = Email::createFromEmailObject($emailOld);
        $this->isNew = $this->email->isNewRecord;
        $this->email->save();

        if ($this->email) {
            $this
                ->fillEmailParams($emailOld->e_priority, $emailOld->e_template_type_id, $emailOld->e_language_id)
                ->fillEmailBody($emailOld->e_email_subject, $emailOld->e_email_body_text, $emailOld->e_email_data)
                ->fillEmailBlob($emailOld->e_email_body_blob, true)
                ->fillEmailContacts(
                    $emailOld->e_email_from,
                    $emailOld->e_email_to,
                    $emailOld->e_email_from_name,
                    $emailOld->e_email_to_name,
                    $emailOld->e_email_cc,
                    $emailOld->e_email_bc
                )
                ->fillEmailLog(
                    $emailOld->e_communication_id,
                    $emailOld->e_message_id,
                    $emailOld->e_error_message,
                    $emailOld->e_status_done_dt,
                    $emailOld->e_read_dt,
                    $emailOld->e_ref_message_id,
                    $emailOld->e_inbox_created_dt,
                    $emailOld->e_inbox_email_id
                )
                ->linkClient($emailOld->e_client_id)
                ->linkCase($emailOld->e_case_id)
                ->linkLead($emailOld->e_lead_id)
                ->linkReply($emailOld->e_reply_id)
            ;
        }

        return $this;
    }

    public function fillEmailParams(int $priority, ?int $templateType, ?string $language)
    {
        $attributes = [
            'ep_template_type_id' => $templateType,
            'ep_language_id' => $language,
            'ep_priority' => $priority,
        ];

        $this->emailParams = $this->email->params ?? new EmailParams();
        $this->emailParams->attributes = $attributes;
        $this->emailParams->save();

        $this->emailParams->link('email', $this->email);

        return $this;
    }

    public function fillEmailLog(
        ?int $communicationId,
        ?string $messageId,
        ?string $errorMessage,
        ?string $statusDoneDt,
        ?string $readDt,
        ?string $refMessageId,
        ?string $inboxCreatedDt,
        ?int $inboxEmailId
    ) {
        $attributes = [
            'el_status_done_dt' => $statusDoneDt,
            'el_read_dt' => $readDt,
            'el_error_message' => $errorMessage,
            'el_message_id' => $messageId,
            'el_ref_message_id' => $refMessageId,
            'el_inbox_created_dt' => $inboxCreatedDt,
            'el_inbox_email_id' => $inboxEmailId,
            'el_communication_id' => $communicationId,
        ];

        $this->emailLog = $this->email->emailLog ?? new EmailLog();
        $this->emailLog->attributes = $attributes;
        $this->emailLog->save();

        $this->emailLog->link('email', $this->email);

        return $this;
    }

    public function fillEmailBody(string $subject, string $body, $emailData)
    {
        $attributes = [
            'embd_email_subject' => $subject,
            'embd_email_body_text' => $body,
            'embd_email_data' => $emailData,
            'embd_hash' => hash('crc32b', join(' | ', [$subject, $body])),
        ];

        $this->emailBody = $this->email->emailBody ?? new EmailBody();
        $this->emailBody->attributes = $attributes;
        $this->emailBody->save();

        $this->email->link('emailBody', $this->emailBody);

        return $this;
    }

    public function fillEmailBlob(string $body, $compressed = false)
    {
        $this->emailBlob = new EmailBlob();
        $this->emailBlob->attributes = ['embb_email_body_blob' => $compressed ? $body : TextConvertingHelper::compress($body)];
        $this->emailBlob->save();

        $this->emailBlob->link('emailBody', $this->emailBody);

        return $this;
    }

    //call after fill Email
    public function linkClient(?int $clientId)
    {
        if ($clientId !== null) {
            $client = Client::findOne($clientId);
            if ($client) {
                $this->email->link('clients', $client);
            }
        }
        return $this;
    }

    public function linkCase(?int $caseId)
    {
        if ($caseId !== null) {
            $case = Cases::findOne($caseId);
            if ($case) {
                $this->email->link('cases', $case);
            }
        }
        return $this;
    }

    public function linkLead(?int $leadId)
    {
        if ($leadId !== null) {
            $lead = Lead::findOne($leadId);
            if ($lead) {
                $this->email->link('leads', $lead);
            }
        }
        return $this;
    }

    public function linkReply(?int $replyId)
    {
        if ($replyId !== null) {
            $reply = Email::findOne($replyId);
            if ($reply) {
                $this->email->link('reply', $reply);
            }
        }
        return $this;
    }

    public function fillEmailContacts(string $emailFrom, string $emailTo, ?string $emailFromName, ?string $emailToName, ?string $emailCc, ?string $emailBcc)
    {
        $addressFrom = $this->getAddress($emailFrom, $emailFromName);
        $contactFrom = new EmailContact();
        $contactFrom->attributes = [
            'ec_address_id' => $addressFrom->ea_id,
            'ec_email_id' => $this->email->e_id,
            'ec_type_id' => EmailContactType::FROM
        ];
        $contactFrom->save();
        $this->emailContacts[] = $contactFrom;

        $addressTo = $this->getAddress($emailTo, $emailToName);
        $contactTo = new EmailContact();
        $contactTo->attributes = [
            'ec_address_id' => $addressTo->ea_id,
            'ec_email_id' => $this->email->e_id,
            'ec_type_id' => EmailContactType::TO
        ];
        $contactTo->save();
        $this->emailContacts[] = $contactTo;

        if (!empty($emailCc)) {
            $addressCc = $this->getAddress($emailCc);
            $contactCc = new EmailContact();
            $contactCc->attributes = [
                'ec_address_id' => $addressCc->ea_id,
                'ec_email_id' => $this->email->e_id,
                'ec_type_id' => EmailContactType::CC
            ];
            $contactCc->save();
            $this->emailContacts[] = $contactCc;
        }

        if (!empty($emailBcc)) {
            $addressBcc = $this->getAddress($emailBcc);
            $contactBcc = new EmailContact();
            $contactBcc->attributes = [
                'ec_address_id' => $addressCc->ea_id,
                'ec_email_id' => $this->email->e_id,
                'ec_type_id' => EmailContactType::BCC
            ];
            $contactBcc->save();
            $this->emailContacts[] = $contactBcc;
        }

        return $this;
    }

    public function getAddress(string $email, ?string $name): EmailAddress
    {
        $attributes = [
            'ea_email' => $email,
            'ea_name' => preg_replace('~\"(.*)\"~iU', "$1", $name),
        ];
        $address = EmailAddress::findOneOrNew(['ea_email' => $email]);
        if ($address->isNewRecord) {
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
            "SELECT COUNT(*) as total
             FROM `".Email::tableName()."` emn
            LEFT JOIN `".EmailOld::tableName()."` emo ON emo.e_id = emn.e_id"
            );
        $result = $command->queryAll();

        return  $result[0]['total'];
    }

    public function getErrors()
    {
        return $this->errors;
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
                $paramsData = [
                    'ep_template_type_id' => $form->params->templateType,
                    'ep_language_id' => $form->params->language,
                    'ep_priority' => $form->params->priority,
                    'ep_email_id' => $email->e_id,
                ];
                EmailParams::create($paramsData);
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
                $address = $this->getAddress($contactForm->email, $contactForm->name);
                EmailContact::create([
                    'ec_address_id' => $address->ea_id,
                    'ec_email_id' => $email->e_id,
                    'ec_type_id' => $contactForm->type
                ]);
            }
            //=!EmailContacts

            //=link Client
            $email->refresh();
            $clientId = (Yii::createObject(EmailService::class))->detectClientId($email->emailTo);
            if ($client = Client::findOne($clientId)) {
                $email->link('clients', $client);
            }
            //=!ink Client

            $email->setMessageId();

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
     * @throws \RuntimeException
     */
    public function sendMail(Email $email, array $data = [])
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
            $request = $communication->mailSend($email->e_project_id, $tplType, $email->emailFrom, $email->emailTo, $content_data, $data, $language, 0);

            if ($request && isset($request['data']['eq_status_id'])) {
                $email->e_status_id = $request['data']['eq_status_id'];
                $email->e_type_id = EmailType::isDraft($email->e_type_id) ? EmailType::OUTBOX : $email->e_type_id;
                $email->saveEmailLog(['el_communication_id' => $request['data']['eq_id']]);
                $email->save();
            }

            if ($request && isset($request['error']) && $request['error']) {
                $errorData = @json_decode($request['error'], true);
                $errorMessage = $errorData['message'] ?: $request['error'];
                throw new \Exception($errorMessage);
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
}

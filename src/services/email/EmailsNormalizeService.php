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

            $transaction->commit();

        }catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return $email;
    }
}

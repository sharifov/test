<?php

namespace src\dto\email;

use src\entities\email\helpers\EmailType;
use src\entities\email\helpers\EmailStatus;
use yii\helpers\ArrayHelper;

/**
 * Class EmailDTO
 * @package src\dto\email
 *
 * @property string $emailFrom
 * @property string $emailFromName
 * @property string $emailTo
 * @property string $emailToName
 * @property string $emailSubject
 * @property string $bodyHtml
 * @property string $createdDt
 * @property int $inboxEmailId
 * @property string $inboxCreatedDt
 * @property string $refMessageId
 * @property string $messageId
 * @property int $projectId
 * @property int $clientId
 * @property int $typeId
 * @property int $templateTypeId
 * @property string $languageId
 * @property int $statusId
 * @property bool $isNew
 * @property int $communicationId
 * @property array|null $attachPaths
 */
class EmailDTO
{
    public $emailFrom;
    public $emailFromName;
    public $emailTo;
    public $emailToName;
    public $emailSubject;
    public $bodyHtml;
    public $createdDt;
    public $inboxEmailId;
    public $inboxCreatedDt;
    public $refMessageId;
    public $messageId;
    public $projectId;
    public $clientId;
    public $typeId;
    public $statusId;
    public $languageId;
    public $templateTypeId;
    public $isNew;
    public $communicationId;
    public $attachPaths;

    public static function newInstance()
    {
        return new static();
    }

    public function fillFromCommunication(array $mail, $typeId = EmailType::INBOX, $statusId = EmailStatus::DONE): EmailDTO
    {
        $this->typeId = $typeId;
        $this->statusId = $statusId;
        $this->isNew = true;
        $this->emailTo = $mail['ei_email_to'];
        $this->emailToName = $mail['ei_email_to_name'] ?? null;
        $this->emailFrom = $mail['ei_email_from'];
        if (isset($mail['ei_email_from_name'])) {
            $this->emailFromName = $this->filter($mail['ei_email_from_name']);
        }
        if (isset($mail['ei_email_subject'])) {
            $this->emailSubject = $this->filter($mail['ei_email_subject']);
        }
        $this->bodyHtml = $mail['ei_email_text'];
        $this->createdDt = $mail['ei_created_dt'];
        $this->inboxEmailId = $mail['ei_id'];
        $this->inboxCreatedDt = $mail['ei_received_dt'] ?: $mail['ei_created_dt'];
        $this->refMessageId = $mail['ei_ref_mess_ids'];
        $this->messageId = $mail['ei_message_id'];
        //$this->communicationId = $mail['ei_id'];

        if ($attachPaths = ArrayHelper::getValue($mail, 'attach_paths')){
            $this->attachPaths = explode(',', $attachPaths);
        }

        return $this;
    }

    private function filter($str)
    {
        if (!$str) {
            return $str;
        }
        return filter_var($str, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    }
}

<?php

namespace src\dto\email;

use src\entities\email\helpers\EmailType;
use src\entities\email\helpers\EmailStatus;
use yii\helpers\ArrayHelper;
use modules\email\src\protocol\gmail\message\Gmail;
use modules\order\src\entities\order\Order;
use common\models\EmailTemplateType;

/**
 * Class EmailDTO
 * @package src\dto\email
 *
 * @property string $emailFrom
 * @property string $emailFromName
 * @property string $emailTo
 * @property string $emailToName
 * @property string $emailCc
 * @property string $emailSubject
 * @property string $bodyHtml
 * @property string $createdDt
 * @property int $inboxEmailId
 * @property string $inboxCreatedDt
 * @property string $refMessageId
 * @property string $messageId
 * @property int $projectId
 * @property int $depId
 * @property int $leadId
 * @property int $caseId
 * @property int $clientId
 * @property int $typeId
 * @property int $templateTypeId
 * @property string $languageId
 * @property int $statusId
 * @property bool $isNew
 * @property int $communicationId
 * @property int $createdUserId
 * @property array|null $attachPaths
 * @property array|null $attachments
 * @property int|null $emailId
 */
class EmailDTO
{
    public $emailFrom;
    public $emailFromName;
    public $emailTo;
    public $emailToName;
    public $emailCc;
    public $emailSubject;
    public $bodyHtml;
    public $createdDt;
    public $inboxEmailId;
    public $inboxCreatedDt;
    public $refMessageId;
    public $messageId;
    public $projectId;
    public $depId;
    public $leadId;
    public $caseId;
    public $clientId;
    public $typeId;
    public $statusId;
    public $languageId;
    public $templateTypeId;
    public $isNew;
    public $communicationId;
    public $createdUserId;
    public $attachPaths;
    public $attachments;
    public $emailId;

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

        if ($attachPaths = ArrayHelper::getValue($mail, 'attach_paths')) {
            $this->attachPaths = explode(',', $attachPaths);
        }

        return $this;
    }

    public function fillFromGmail(Gmail $gmail, string $emailTo, $typeId = EmailType::INBOX, $statusId = EmailStatus::DONE): EmailDTO
    {
        $this->typeId = $typeId;
        $this->statusId = $statusId;
        $this->isNew = true;
        $this->emailTo = $emailTo;
        $this->emailFrom = $gmail->getFromEmail();
        $this->emailFromName = $gmail->getFromName();
        $this->emailSubject = $gmail->getSubject();
        $this->bodyHtml = $gmail->getContent();
        $this->createdDt = date('Y-m-d H:i:s');
        $this->inboxCreatedDt = $gmail->getDate();
        $this->refMessageId = $gmail->getReferences();
        $this->messageId = $gmail->getMessageId();

        return $this;
    }

    public function fillFromOrderConfirm(
        Order $order,
        $templateKey,
        $from,
        $fromName,
        $to,
        $languageId,
        $subject,
        $body,
        array $attachments = [],
        $typeId = EmailType::OUTBOX,
        $statusId = EmailStatus::PENDING
    ) {
        $this->projectId = $order->or_project_id;
        $this->leadId = $order->or_lead_id;
        $this->typeId = $typeId;
        $this->statusId = $statusId;
        $this->isNew = true;
        $this->emailTo = $to;
        $this->emailFrom = $from;
        $this->emailFromName = $fromName;
        $this->languageId = $languageId;
        $this->emailSubject = $subject;
        $this->bodyHtml = $body;
        $this->createdDt = date('Y-m-d H:i:s');
        $this->templateTypeId = $this->getTemplateIdByKey($templateKey);
        $this->attachments = $attachments;

        return $this;
    }

    /**
     *
     * @param array $data
     * @return \src\dto\email\EmailDTO
     */
    public static function fromArray(array $data)
    {
        $instance = new static();
        $instance->projectId = $data['projectId'] ?? null;
        $instance->leadId = $data['leadId'] ?? null;
        $instance->caseId = $data['caseId'] ?? null;
        $instance->depId = $data['depId'] ?? null;
        $instance->clientId = $data['clientId'] ?? null;
        $instance->type = $data['typeId'] ?? EmailType::OUTBOX;
        $instance->statusId = $data['statusId'] ?? EmailStatus::PENDING;
        $instance->isNew = true;
        $instance->emailTo = $data['emailTo'] ?? null;
        $instance->emailToName = $data['emailToName'] ?? null;
        $instance->emailFrom = $data['emailFrom'] ?? null;
        $instance->emailFromName = $data['emailFromName'] ?? null;
        $instance->emailCc = !empty($data['emailCc']) ? $data['emailCc'] : null;
        $instance->languageId =  $data['languageId'] ?? null;
        $instance->emailSubject = $data['emailSubject'] ?? null;
        $instance->bodyHtml = $data['bodyHtml'] ?? null;
        $instance->createdDt = $data['createdDt'] ?? date('Y-m-d H:i:s');
        $instance->createdUserId = $data['createdUserId'] ?? null;
        $instance->templateTypeId = $data['templateTypeId'] ?? ($data['templateKey'] ? $this->getTemplateIdByKey($templateKey) : null);
        $instance->inboxEmailId = $data['inboxEmailId'] ?? null;
        $instance->inboxCreatedDt = $data['inboxCreatedDt'] ?? null;
        $instance->refMessageId = $data['refMessageId'] ?? null;
        $instance->messageId = $data['messageId'] ?? null;
        $instance->emailId = $data['emailId'] ?? null;

        return $instance;
    }

    /**
     *
     * @param string $templateKey
     * @return int|null
     */
    private function getTemplateIdByKey(string $templateKey)
    {
        $templateTypeId = EmailTemplateType::find()
            ->select(['etp_id'])
            ->andWhere(['etp_key' => $templateKey])
            ->asArray()
            ->limit(1)
            ->one();

        return $templateTypeId['etp_id'] ?? null;
    }

    private function filter($str)
    {
        if (!$str) {
            return $str;
        }
        return filter_var($str, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
    }
}

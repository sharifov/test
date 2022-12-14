<?php

namespace src\entities\email\form;

use yii\base\Model;
use src\entities\email\EmailLog;

class EmailLogForm extends Model
{
    use FormAttributesTrait;

    public $id;
    public $statusDoneDt;
    public $readDt;
    public $errorMessage;
    public $messageId;
    public $refMessageId;
    public $inboxCreatedDt;
    public $inboxEmailId;
    public $communicationId;
    public $isNew;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public static function fromArray(array $data): EmailLogForm
    {
        $instance = new static();
        $instance->setAttributes($data);
        return $instance;
    }

    public static function fromModel(EmailLog $log, $config = []): EmailLogForm
    {
        $instance = new static($config);
        $instance->id = $log->el_id;
        $instance->statusDoneDt = $log->el_status_done_dt;
        $instance->readDt = $log->el_read_dt;
        $instance->errorMessage = $log->el_error_message;
        $instance->messageId = $log->el_message_id;
        $instance->refMessageId = $log->el_ref_message_id;
        $instance->inboxCreatedDt = $log->el_inbox_created_dt;
        $instance->inboxEmailId = $log->el_inbox_email_id;
        $instance->communicationId = $log->el_communication_id;
        $instance->isNew = $log->el_is_new;

        return $instance;
    }

    public static function replyFromModel(EmailLog $log, $config = []): EmailLogForm
    {
        return new static($config);
    }

    public function attributes(): array
    {
        return [
            'communicationId',
            'inboxEmailId',
            'isNew',
            'errorMessage',
            'messageId',
            'refMessageId',
            'inboxCreatedDt',
            'readDt',
            'statusDoneDt',
            'id'
        ];
    }

    public function fields(): array
    {
        return [
            'el_communication_id' => 'communicationId',
            'el_inbox_email_id' => 'inboxEmailId',
            'el_is_new' => 'isNew',
            'el_error_message' => 'errorMessage',
            'el_message_id' => 'messageId',
            'el_ref_message_id' => 'refMessageId',
            'el_inbox_created_dt' => 'inboxCreatedDt',
            'el_read_dt' => 'readDt',
            'el_status_done_dt' => 'statusDoneDt',
            'el_id' => 'id',
        ];
    }

    public function rules(): array
    {
        return [
            [['communicationId','inboxEmailId', 'isNew'], 'integer'],
            [['errorMessage', 'messageId', 'refMessageId'], 'string'],
            [['inboxCreatedDt', 'readDt', 'statusDoneDt'], 'safe'],
        ];
    }
}

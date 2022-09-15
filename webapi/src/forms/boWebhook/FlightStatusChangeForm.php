<?php

namespace webapi\src\forms\boWebhook;

use yii\base\Model;

class FlightStatusChangeForm extends Model implements WebhookFromDefinitions
{
    public $order_uid;
    public $status;
    public $lead_id;
    public $client_phone;
    public $client_email;
    public $project_key;
    public $source_code;
    public $flight_id;
    public $flight_cabin;
    public $user_language;
    public $currency_code;

    public function rules(): array
    {
        return [
            [['order_uid', 'status', 'project_key'], 'required'],
            [['order_uid', 'client_email', 'client_phone', 'project_key', 'source_code', 'user_language', 'currency_code', 'status', 'flight_cabin'], 'string'],
            [['lead_id', 'flight_id'], 'integer'],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function isStatusPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isStatusProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isStatusRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isStatusVoid(): bool
    {
        return $this->status === self::STATUS_VOID;
    }

    public function isStatusClose(): bool
    {
        return $this->status === self::STATUS_CLOSE;
    }

    public function hasLead(): bool
    {
        return !empty($this->leadId);
    }
}

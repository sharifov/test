<?php

namespace webapi\src\forms\boWebhook;

use common\models\Project;
use yii\base\Model;

/**
 * Class ReprotectionUpdateForm
 *
 * @property string $booking_id
 * @property string $project_key
 * @property string $reprotection_quote_gid
 * @property string $status
 * @property string $orderId
 */
class ReprotectionUpdateForm extends Model implements WebhookFromDefinitions
{
    public $booking_id;
    public $project_key;
    public $reprotection_quote_gid;
    public $status;
    public $orderId;

    public function rules(): array
    {
        return [
            [['booking_id', 'reprotection_quote_gid', 'status'], 'required'],
            [['orderId'], 'safe'],

            [['booking_id', 'reprotection_quote_gid', 'orderId'], 'string'],

            [['project_key'], 'string', 'max' => 50],
            ['project_key', 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['project_key' => 'project_key'], 'skipOnError' => true],

            [['status'], 'string'],
            [['status'], 'in', 'range' => self::EXCHANGE_STATUS_LIST],
        ];
    }

    public function formName(): string
    {
        return '';
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isExchanged(): bool
    {
        return $this->status === self::STATUS_EXCHANGED;
    }

    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }
}

<?php

namespace webapi\src\forms\boWebhook;

use common\models\Project;
use yii\base\Model;

/**
 * Class FlightRefundUpdateForm
 * @package webapi\src\boWebhook
 *
 * @property string $booking_id
 * @property string $project_key
 * @property string $status
 * @property string $orderId
 */
class FlightRefundUpdateForm extends Model implements WebhookFromDefinitions
{
    public $booking_id;
    public $project_key;
    public $status;
    public $orderId;

    public function rules(): array
    {
        return [
            [['booking_id', 'status', 'project_key'], 'required'],
            [['orderId'], 'safe'],

            [['booking_id', 'project_key', 'orderId', 'status'], 'string'],

            [['status'], 'in', 'range' => self::REFUND_STATUS_LIST],

            [['project_key'], 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['project_key' => 'project_key'], 'skipOnError' => true],
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

    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    public function isCanceled(): bool
    {
        return $this->status === self::STATUS_CANCELED;
    }
}

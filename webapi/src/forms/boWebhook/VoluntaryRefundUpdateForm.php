<?php

namespace webapi\src\forms\boWebhook;

use common\models\Project;

/**
 * Class VoluntaryRefundUpdateForm
 * @package webapi\src\forms\boWebhook
 *
 * @property string $booking_id
 * @property string $project_key
 * @property string $status
 * @property string $orderId
 */
class VoluntaryRefundUpdateForm extends \yii\base\Model
{
    private const STATUS_PROCESSING = 'Processing';
    private const STATUS_REFUNDED = 'Refunded';
    private const STATUS_CANCELED = 'Canceled';

    private const STATUS_LIST = [
        self::STATUS_PROCESSING,
        self::STATUS_REFUNDED,
        self::STATUS_CANCELED,
    ];

    public $booking_id;
    public $project_key;
    public $status;
    public $orderId;

    public function rules(): array
    {
        return [
            [['booking_id', 'project_key', 'status', 'orderId'], 'required'],
            [['booking_id'], 'string', 'max' => 50],

            [['project_key'], 'string', 'max' => 255],
            [['project_key'], 'exist', 'targetClass' => Project::class, 'targetAttribute' => ['project_key' => 'project_key'], 'skipOnError' => true],

            [['status'], 'string'],
            [['status'], 'in', 'range' => self::STATUS_LIST],

            [['orderId'], 'string']
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

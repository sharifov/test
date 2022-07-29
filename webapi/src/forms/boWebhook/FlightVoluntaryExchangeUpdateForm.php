<?php

namespace webapi\src\forms\boWebhook;

use common\models\Project;

/**
 * Class FlightRefundUpdateForm
 *
 * @property $booking_id
 * @property $project_key
 * @property $status
 */
class FlightVoluntaryExchangeUpdateForm extends \yii\base\Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_EXCHANGED = 'exchanged';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_PROCESSING = 'processing';

    public const STATUS_LIST = [
        self::STATUS_PENDING => self::STATUS_PENDING,
        self::STATUS_EXCHANGED => self::STATUS_EXCHANGED,
        self::STATUS_CANCELED => self::STATUS_CANCELED,
        self::STATUS_PROCESSING => self::STATUS_PROCESSING,
    ];

    public $booking_id;
    public $project_key;
    public $status;
    public $quote_gid;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string'],

            [['quote_gid'], 'string', 'max' => 32],

            [['project_key'], 'string'],
            [['project_key'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_key' => 'project_key']],

            [['status'], 'required'],
            [['status'], 'string'],
            [['status'], 'filter', 'filter' => 'strtolower', 'skipOnError' => true],
            [['status'], 'in', 'range' => self::STATUS_LIST],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

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
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_EXCHANGED = 'exchanged';
    public const STATUS_CANCELED = 'canceled';

    public const STATUS_LIST = [
        self::STATUS_PROCESSING => self::STATUS_PROCESSING,
        self::STATUS_EXCHANGED => self::STATUS_EXCHANGED,
        self::STATUS_CANCELED => self::STATUS_CANCELED,
    ];

    public $booking_id;
    public $project_key;
    public $status;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string'],

            [['project_key'], 'string'],
            [['project_key'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_key' => 'api_key']],

            [['status'], 'required'],
            [['status'], 'string'],
            [['status'], 'in', 'range' => self::STATUS_LIST],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

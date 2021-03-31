<?php

namespace modules\flight\src\forms;

use frontend\helpers\JsonHelper;
use modules\flight\models\FlightQuote;
use modules\flight\src\services\api\FlightUpdateRequestApiService;
use yii\base\Model;

/**
 * Class TicketIssueFlightApiForm
 * @property $uniqueId
 * @property $status
 */
class TicketIssueFlightApiForm extends Model
{
    public $uniqueId;
    public $status;

    public function rules(): array
    {
        return [
            ['uniqueId', 'required'],
            ['uniqueId', 'trim'],
            ['uniqueId', 'string', 'max' => 255],
            [['uniqueId'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['uniqueId' => 'fq_flight_request_uid']],

            ['status', 'required'],
            ['status', 'integer'],
            ['status', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],
            [
                'status',
                'compare', 'compareValue' => FlightUpdateRequestApiService::SUCCESS_STATUS, 'operator' => '==', 'type' => 'number',
                'message' => '{attribute} value is not equal success status'
            ],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

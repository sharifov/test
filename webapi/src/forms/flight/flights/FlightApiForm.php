<?php

namespace  webapi\src\forms\flight\flights;

use common\components\SearchService;
use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\flight\src\services\api\FlightUpdateRequestApiService;
use modules\order\src\entities\order\Order;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\flight\flights\bookingInfo\BookingInfoApiForm;
use yii\base\Model;

/**
 * Class FlightApiForm
 *
 * @property $uniqueId
 * @property $status
 * @property $pnr
 * @property $gds
 * @property $flightType
 * @property $validatingCarrier
 * @property $bookingInfo
 * @property $trips
 *
 * @property $bookingInfoForms
 */
class FlightApiForm extends Model
{
    public $uniqueId;
    public $status;
    public $pnr;
    public $gds;
    public $flightType;
    public $validatingCarrier;
    public $bookingInfo;
    public $trips;

    private array $bookingInfoForms = [];

    public function rules(): array
    {
        return [
            [['uniqueId'], 'required'],

            [['flightType', 'uniqueId', 'pnr', 'gds', 'validatingCarrier'], 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            [['flightType', 'uniqueId', 'pnr', 'gds', 'validatingCarrier'], 'filter', 'filter' => 'strtoupper', 'skipOnEmpty' => true],

            [['uniqueId'], 'string', 'max' => 50],

            [['status'], 'integer'],
            [['status'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [['status'], 'in', 'range' => array_keys(FlightUpdateRequestApiService::STATUS_LIST)],

            [['pnr'], 'string', 'max' => 100, 'skipOnEmpty' => true],

            [['gds'], 'string', 'max' => 1],

            [['flightType'], 'string', 'min' => 2, 'max' => 2, 'skipOnEmpty' => true],

            [['validatingCarrier'], 'string', 'max' => 2],

            [['bookingInfo'], 'required'],
            [['bookingInfo'], CheckJsonValidator::class],
            [['bookingInfo'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['bookingInfo'], 'checkBookingInfo'],

            [['trips'], 'required'],
            [['trips'], CheckJsonValidator::class],
            [['trips'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['trips'], 'checkTrips'],
        ];
    }

    public function checkBookingInfo($attribute): void
    {
        foreach ($this->bookingInfo as $key => $bookingInfo) {
            $bookingInfoApiForm = new BookingInfoApiForm();
            if (!$bookingInfoApiForm->load($bookingInfo)) {
                $this->addError($attribute, 'BookingInfoApiForm is not loaded');
                break;
            }
            if (!$bookingInfoApiForm->validate()) {
                $this->addError($attribute, 'BookingInfoApiForm: ' . ErrorsToStringHelper::extractFromModel($bookingInfoApiForm));
                break;
            }
            $this->bookingInfoForms[$key] = $bookingInfoApiForm;
        }
    }

    public function checkTrips($attribute): void
    {
        foreach ($this->trips as $key => $trip) {
            $x = true; /* TODO::  */
        }
    }

    public function formName(): string
    {
        return '';
    }
}

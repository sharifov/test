<?php

namespace  webapi\src\forms\flight\flights;

use common\components\SearchService;
use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\src\services\api\FlightUpdateRequestApiService;
use modules\order\src\entities\order\Order;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\flight\flights\bookingInfo\BookingInfoApiForm;
use webapi\src\forms\flight\flights\price\PriceApiForm;
use webapi\src\forms\flight\flights\trips\SegmentApiForm;
use yii\base\Model;
use yii\helpers\ArrayHelper;

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
 * @property $price
 *
 * @property BookingInfoApiForm[] $bookingInfoForms
 * @property PriceApiForm|null $priceApiForm
 * @property array $tripSegments
 * @property $originalDataJson
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
    public $price;

    private array $bookingInfoForms = [];
    private $priceApiForm;
    private $originalDataJson;
    private array $tripSegments = [];

    public function __construct($originalDataJson, $config = [])
    {
        $this->originalDataJson = $originalDataJson;
        parent::__construct($config);
    }

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

            [['pnr'], 'string', 'max' => 100],
            [['gds'], 'string', 'max' => 1],

            [['flightType'], 'string', 'min' => 2, 'max' => 2, 'skipOnEmpty' => true],
            [['flightType'], 'in', 'range' => FlightQuoteFlight::TRIP_TYPE_LIST],

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

            [['price'], CheckJsonValidator::class],
            [['price'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['price'], 'checkPrice'],
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
                $this->addError($attribute, 'BookingInfoApiForm: ' . ErrorsToStringHelper::extractFromModel($bookingInfoApiForm, ', '));
                break;
            }
            $this->bookingInfoForms[$key] = $bookingInfoApiForm;
        }
    }

    public function checkPrice($attribute): void
    {
        $priceApiForm = new PriceApiForm();
        if (!$priceApiForm->load($this->price)) {
            $this->addError($attribute, 'PriceApiForm is not loaded');
        }
        if (!$priceApiForm->validate()) {
            $this->addError($attribute, 'PriceApiForm: ' . ErrorsToStringHelper::extractFromModel($priceApiForm, ', '));
        }
        if (!$this->hasErrors($attribute)) {
            $this->priceApiForm = $priceApiForm;
        }
    }

    public function checkTrips($attribute): void
    {
        foreach ($this->trips as $tripKey => $trip) {
            if (!$segments = ArrayHelper::getValue($trip, 'segments')) {
                $this->addError($attribute, 'Segments is required');
            }
            foreach ($segments as $segment) {
                $segmentApiForm = new SegmentApiForm($tripKey);
                if (!$segmentApiForm->load($segment)) {
                    $this->addError($attribute, 'SegmentApiForm is not loaded');
                    break;
                }
                if (!$segmentApiForm->validate()) {
                    $this->addError($attribute, 'SegmentApiForm: ' . ErrorsToStringHelper::extractFromModel($segmentApiForm, ', '));
                    break;
                }
                $this->tripSegments[$tripKey][] = $segmentApiForm;
            }
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function getBookingInfoForms(): array
    {
        return $this->bookingInfoForms;
    }

    public function getOriginalDataJson()
    {
        return $this->originalDataJson;
    }

    public function getPriceApiForm(): ?PriceApiForm
    {
        return $this->priceApiForm;
    }

    public function getTripSegments(): array
    {
        return $this->tripSegments;
    }

    public function isIssued(): bool
    {
        return $this->status === FlightUpdateRequestApiService::SUCCESS_STATUS;
    }
}

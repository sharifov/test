<?php

namespace  webapi\src\forms\flight\flights\bookingInfo;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use modules\flight\src\services\api\FlightUpdateRequestApiService;
use sales\helpers\ErrorsToStringHelper;
use webapi\src\forms\flight\flights\bookingInfo\airlinesCode\AirlinesCodeApiForm;
use webapi\src\forms\flight\flights\bookingInfo\insurance\InsuranceApiForm;
use webapi\src\forms\flight\flights\bookingInfo\passengers\PassengerApiForm;
use yii\base\Model;

/**
 * Class BookingInfoApiForm
 *
 * @property $bookingId
 * @property $status
 * @property $pnr
 * @property $gds
 * @property $flightType
 * @property $validatingCarrier
 * @property $passengers
 * @property $airlinesCode
 * @property $insurance
 * @property PassengerApiForm[] $passengerForms
 * @property AirlinesCodeApiForm[] $airlinesCodeForms
 * @property InsuranceApiForm|null $insuranceApiForm
 */
class BookingInfoApiForm extends Model
{
    public $bookingId;
    public $status;
    public $pnr;
    public $gds;
    public $flightType;
    public $validatingCarrier;
    public $passengers;
    public $airlinesCode;
    public $insurance;

    private array $passengerForms = [];
    private array $airlinesCodeForms = [];
    private $insuranceApiForm;

    public function rules(): array
    {
        return [
            [['bookingId'], 'required'],

            [['bookingId', 'pnr', 'gds', 'validatingCarrier'], 'filter', 'filter' => 'trim', 'skipOnEmpty' => true],
            [['bookingId', 'pnr', 'gds', 'validatingCarrier'], 'filter', 'filter' => 'strtoupper', 'skipOnEmpty' => true],

            [['bookingId'], 'string', 'max' => 50],
            [['pnr'], 'string', 'max' => 100, 'skipOnEmpty' => true],
            [['gds'], 'string', 'max' => 1],
            [['validatingCarrier'], 'string', 'max' => 2],

            [['status'], 'integer'],
            [['status'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [['status'], 'in', 'range' => array_keys(FlightUpdateRequestApiService::STATUS_LIST)],

            [['passengers'], 'required'],
            [['passengers'], CheckJsonValidator::class],
            [['passengers'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['passengers'], 'checkPassengers'],

            [['airlinesCode'], 'required'],
            [['airlinesCode'], CheckJsonValidator::class],
            [['airlinesCode'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['airlinesCode'], 'checkAirlinesCode'],

            [['insurance'], CheckJsonValidator::class],
            [['insurance'], 'filter', 'filter' => static function ($value) {
                return JsonHelper::decode($value);
            }],
            [['insurance'], 'checkInsurance'],
        ];
    }

    public function checkPassengers($attribute): void
    {
        foreach ($this->passengers as $key => $value) {
            $passengerApiForm = new PassengerApiForm($key);
            if (!$passengerApiForm->load($value)) {
                $this->addError($attribute, 'PassengerApiForm is not loaded');
                break;
            }
            if (!$passengerApiForm->validate()) {
                $this->addError($attribute, 'PassengerApiForm: ' . ErrorsToStringHelper::extractFromModel($passengerApiForm));
                break;
            }
            $this->passengerForms[$key] = $passengerApiForm;
        }
    }

    public function checkAirlinesCode($attribute): void
    {
        foreach ($this->airlinesCode as $key => $value) {
            $airlinesCodeApiForm = new AirlinesCodeApiForm();
            if (!$airlinesCodeApiForm->load($value)) {
                $this->addError($attribute, 'AirlinesCodeApiForm is not loaded');
                break;
            }
            if (!$airlinesCodeApiForm->validate()) {
                $this->addError($attribute, 'AirlinesCodeApiForm: ' . ErrorsToStringHelper::extractFromModel($airlinesCodeApiForm));
                break;
            }
            $this->airlinesCodeForms[$key] = $airlinesCodeApiForm;
        }
    }

    public function checkInsurance($attribute): void
    {
        if (!empty($this->insurance)) {
            $insuranceApiForm = new InsuranceApiForm();
            if (!$insuranceApiForm->load($this->insurance)) {
                $this->addError($attribute, 'InsuranceApiForm is not loaded');
            }
            if (!$insuranceApiForm->validate()) {
                $this->addError($attribute, 'InsuranceApiForm: ' . ErrorsToStringHelper::extractFromModel($insuranceApiForm));
            }
            if (!$this->hasErrors($attribute)) {
                $this->insuranceApiForm = $insuranceApiForm;
            }
        }
    }

    public function isIssued(): bool
    {
        return $this->status === FlightUpdateRequestApiService::SUCCESS_STATUS;
    }

    public function formName(): string
    {
        return '';
    }

    public function getPassengerForms(): array
    {
        return $this->passengerForms;
    }

    public function getAirlinesCodeForms(): array
    {
        return $this->airlinesCodeForms;
    }

    public function getInsuranceApiForm(): ?InsuranceApiForm
    {
        return $this->insuranceApiForm;
    }
}

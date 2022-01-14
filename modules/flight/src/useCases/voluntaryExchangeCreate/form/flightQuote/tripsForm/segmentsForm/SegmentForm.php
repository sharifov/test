<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote\tripsForm\segmentsForm;

use common\components\validators\CheckAndConvertToJsonValidator;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote\tripsForm\segmentsForm\stops\StopForm;
use src\helpers\ErrorsToStringHelper;
use src\traits\FormNameModelTrait;
use yii\base\Model;

/**
 * Class ReProtectionFlightQuoteForm
 *
 * @property StopForm[]|null $stopForms
 */
class SegmentForm extends Model
{
    use FormNameModelTrait;

    public $departureTime;
    public $arrivalTime;
    public $flightNumber;
    public $bookingClass;
    public $stop;
    public $stops;
    public $duration;
    public $departureAirportCode;
    public $departureAirportTerminal;
    public $arrivalAirportCode;
    public $arrivalAirportTerminal;
    public $operatingAirline;
    public $airEquipType;
    public $marketingAirline;
    public $marriageGroup;
    public $mileage;
    public $meal;
    public $fareCode;
    public $baggage;
    public $cabin;
    public $recheckBaggage;

    private array $stopForms = [];

    public function rules(): array
    {
        return [
            [['departureTime', 'arrivalTime', 'departureAirportCode', 'arrivalAirportCode'], 'required'],

            [['departureTime', 'arrivalTime'], 'datetime', 'format' => 'php:Y-m-d H:i'],

            [['departureAirportCode', 'arrivalAirportCode'], 'string', 'max' => 3],

            ['flightNumber', 'integer'],

            ['bookingClass', 'string', 'max' => 1],

            ['stop', 'integer'],

            [['stops'], CheckAndConvertToJsonValidator::class, 'skipOnEmpty' => true],
            [['stops'], 'stopsProcessing'],

            ['duration', 'integer'],

            [['departureAirportTerminal', 'arrivalAirportTerminal'], 'string', 'max' => 3],

            [['operatingAirline', 'marketingAirline'], 'string', 'max' => 2],

            ['airEquipType', 'string', 'max' => 30],

            ['marriageGroup', 'string', 'max' => 3],

            ['mileage', 'integer'],

            ['meal', 'string', 'max' => 2],

            ['fareCode', 'string', 'max' => 50],

            ['recheckBaggage', 'boolean'],

            ['cabin', 'string'],
        ];
    }

    public function stopsProcessing(string $attribute): void
    {
        if (!empty($this->stops)) {
            foreach ($this->stops as $key => $value) {
                $form = new StopForm();
                $form->setFormName('');
                if (!$form->load($value)) {
                    $this->addError($attribute, 'StopForm not loaded');
                } elseif (!$form->validate()) {
                    $this->addError($attribute, 'StopForm.' . $key . '.' . ErrorsToStringHelper::extractFromModel($form, ', '));
                } else {
                    $this->stopForms[] = $form;
                }
            }
        }
    }

    public function getStopForms(): array
    {
        return $this->stopForms;
    }
}

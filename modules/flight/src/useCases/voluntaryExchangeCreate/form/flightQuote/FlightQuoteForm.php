<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote;

use common\components\validators\CheckJsonValidator;
use common\components\validators\IsArrayValidator;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote\tripsForm\TripForm;
use src\helpers\ErrorsToStringHelper;
use yii\base\Model;

/**
 * Class FlightQuoteForm
 *
 * @property $gds
 * @property $pcc
 * @property $validatingCarrier
 * @property $fareType
 * @property $trips
 * @property $itineraryDump
 *
 * @property TripForm[] $tripForms
 */
class FlightQuoteForm extends Model
{
    public $gds;
    public $pcc;
    public $validatingCarrier;
    public $fareType;
    public $trips;
    public $itineraryDump;

    private array $tripForms = [];

    public function rules(): array
    {
        return [
            [['gds', 'pcc', 'validatingCarrier', 'fareType', 'trips'], 'required'],

            [['gds'], 'string', 'max' => 2],

            [['pcc'], 'string', 'max' => 10],

            [['validatingCarrier'], 'string', 'max' => 2],

            [['fareType'], 'string', 'max' => 50],

            [['trips'], CheckJsonValidator::class, 'skipOnError' => true],
            [['trips'], 'checkTripForms'],

            [['itineraryDump'], IsArrayValidator::class, 'skipOnEmpty' => true],
        ];
    }

    public function checkTripForms($attribute)
    {
        foreach ($this->trips as $key => $trip) {
            $tripForm = new TripForm();
            if (!$tripForm->load($trip)) {
                $this->addError($attribute, 'TripForm not loaded');
            } elseif (!$tripForm->validate()) {
                $this->addError($attribute, 'TripForm: ' . ErrorsToStringHelper::extractFromModel($tripForm, ' '));
            } else {
                $this->tripForms[] = $tripForm;
            }
        }
    }

    public function formName(): string
    {
        return '';
    }
}

<?php

namespace modules\flight\src\useCases\voluntaryExchangeCreate\form\exchange;

use common\components\validators\CheckAndConvertToJsonValidator;
use common\models\Currency;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\flightQuote\tripsForm\TripForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\price\VoluntaryExchangePriceForm;
use src\helpers\ErrorsToStringHelper;
use src\traits\FormNameModelTrait;
use yii\base\Model;

/**
 * Class ExchangeForm
 *
 * @property VoluntaryExchangePriceForm|null $voluntaryExchangePriceForm
 * @property TripForm[]|null $tripForms
 */
class ExchangeForm extends Model
{
    use FormNameModelTrait;

    public $paxCnt;
    public $gds;
    public $pcc;
    public $validatingCarrier;
    public $fareType;
    public $cabin;
    public $currency;
    public $passengers;
    public $prices;
    public $trips;
    public $tickets;

    private array $tripForms = [];
    private ?VoluntaryExchangePriceForm $voluntaryExchangePriceForm = null;
    private array $exchangePassengerForm = [];

    public function rules(): array
    {
        return [
            [['paxCnt'], 'integer'],

            [['paxCnt'], 'integer'],

            [['gds'], 'required'],
            [['gds'], 'string', 'max' => 2],

            [['pcc'], 'string', 'max' => 10],

            [['validatingCarrier'], 'string', 'max' => 2],

            [['fareType'], 'string', 'max' => 50],

            [['cabin'], 'string'],

            ['currency', 'required'],
            ['currency', 'string', 'max' => 3],
            ['currency', 'exist', 'targetClass' => Currency::class, 'targetAttribute' => 'cur_code'],

            [['prices'], CheckAndConvertToJsonValidator::class],
            [['prices'], 'pricesProcessing', 'skipOnEmpty' => true, 'skipOnError' => true],

            [['trips'], 'required'],
            [['trips'], CheckAndConvertToJsonValidator::class, 'skipOnError' => true],
            [['trips'], 'tripsProcessing'],

            [['passengers'], CheckAndConvertToJsonValidator::class, 'skipOnError' => true, 'skipOnEmpty' => true],
            [['passengers'], 'passengersProcessing'],

            [['tickets'], 'required'],
            [['tickets'], CheckAndConvertToJsonValidator::class, 'skipOnError' => true],
        ];
    }

    public function passengersProcessing(string $attribute): void
    {
        if (!empty($this->passengers)) {
            foreach ($this->passengers as $paxCode => $paxPrice) {
                $exchangePassengerForm = new ExchangePassengerForm($paxCode);
                $exchangePassengerForm->setFormName('');
                if (!$exchangePassengerForm->load($paxPrice)) {
                    $this->addError($attribute, 'ExchangePassengerForm not loaded');
                } elseif (!$exchangePassengerForm->validate()) {
                    $this->addError($attribute, 'ExchangePassengerForm.' . $paxCode . '.' .
                        ErrorsToStringHelper::extractFromModel($exchangePassengerForm, ' '));
                } else {
                    $this->exchangePassengerForm[] = $exchangePassengerForm;
                }
            }
        }
    }

    public function tripsProcessing(string $attribute): void
    {
        if (!empty($this->trips)) {
            foreach ($this->trips as $key => $trip) {
                $tripForm = new TripForm();
                $tripForm->setFormName('');
                if (!$tripForm->load($trip)) {
                    $this->addError($attribute, 'TripForm not loaded');
                } elseif (!$tripForm->validate()) {
                    $this->addError($attribute, 'TripForm.' . $key . '.' . ErrorsToStringHelper::extractFromModel($tripForm, ' '));
                } else {
                    $this->tripForms[] = $tripForm;
                }
            }
        }
    }

    public function pricesProcessing(string $attribute): void
    {
        if (!empty($this->prices)) {
            $form = new VoluntaryExchangePriceForm();
            $form->setFormName('');
            if (!$form->load($this->prices)) {
                $this->addError($attribute, 'VoluntaryExchangePriceForm not loaded');
            } elseif (!$form->validate()) {
                $this->addError($attribute, 'VoluntaryExchangePriceForm: ' . ErrorsToStringHelper::extractFromModel($form, ' '));
            } else {
                $this->voluntaryExchangePriceForm = $form;
            }
        }
    }

    public function getVoluntaryExchangePriceForm(): ?VoluntaryExchangePriceForm
    {
        return $this->voluntaryExchangePriceForm;
    }

    public function getTripForms(): array
    {
        return $this->tripForms;
    }
}

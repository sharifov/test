<?php

namespace modules\quoteAward\src\forms;

use common\models\Quote;
use common\models\QuotePrice;
use modules\quoteAward\src\dictionary\AwardProgramDictionary;
use modules\quoteAward\src\entities\QuoteFlightProgramQuery;
use modules\quoteAward\src\models\FlightAwardQuoteItem;
use yii\base\Model;

class PriceListAwardQuoteForm extends Model
{
    public $flight;
    public $passenger_type;
    public $passenger_count = 1;
    public $selling = 0;
    public $net = 0;
    public $taxes = 0;
    public $fare = 0;
    public $mark_up = 0;

    public $is_required_award_program;
    public $miles = 0;
    public $oldParams;

    public $ppm;

    private int $defaultPrecision = 2;

    public function __construct(FlightAwardQuoteItem $flight, string $passenger_type, int $passenger_count, bool $is_required_award_program = false, $config = [])
    {
        $this->flight = $flight->id;
        $this->ppm = $flight->ppm;
        $this->passenger_type = $passenger_type;
        $this->passenger_count = $passenger_count;
        $this->is_required_award_program = $is_required_award_program;
        $this->oldParams = serialize($this->attributes);
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['flight', 'passenger_type', 'selling', 'fare', 'net', 'taxes', 'passenger_count', 'passenger_count', 'oldParams'], 'required'],
            ['net', 'number', 'min' => 1, 'tooSmall' => 'Net price must be greater than 0'],
            ['fare', 'number', 'min' => 1, 'tooSmall' => 'Fare must be greater than 0'],
            ['taxes', 'number', 'min' => 1, 'tooSmall' => 'Taxes must be greater than 0'],
            [['miles', 'mark_up'], 'integer'],
            ['passenger_type', 'in', 'range' => [QuotePrice::PASSENGER_ADULT, QuotePrice::PASSENGER_CHILD, QuotePrice::PASSENGER_INFANT]]
        ];
    }

    public function setParams(array $params)
    {
        $this->setAttributes($params);
    }

    public function calculatePrice(bool $checkPayment, FlightAwardQuoteForm $form, bool $refresh = false)
    {
        $oldAttributes = unserialize($this->oldParams, ['allowed_classes' => false]);
        $this->oldParams = '';
        $this->toFloat();
        if (!$checkPayment) {
            $serviceFee = 0;
        } else {
            $serviceFee = (new Quote())->serviceFee;
        }

        if ($oldAttributes['selling'] != $this->selling || $refresh) {
            $this->mark_up = $this->selling / (1 + $serviceFee) - $this->net; // Selling Price/(1+SERVICE_FEE) - Net Price
        } elseif ($oldAttributes['fare'] != $this->fare) {
            $this->net = $this->fare + $this->taxes;
            if ($form->ppm > 0) {
                $this->miles = $this->fare / $form->ppm;
            }
            $this->selling = ($this->fare + $this->taxes + $this->mark_up) * (1 + $serviceFee); // Selling Price = (Fare + Taxes + Mark-up)*(1+SERVICE_FEE)
        } elseif ($oldAttributes['miles'] != $this->miles) {
            $this->fare = $this->miles * $form->ppm;
            $this->net = $this->fare + $this->taxes;
            $this->selling = ($this->fare + $this->taxes + $this->mark_up) * (1 + $serviceFee);
        } elseif ($oldAttributes['ppm'] != $form->ppm) {
            $this->fare = $this->miles * $form->ppm;
            $this->net = $this->fare + $this->taxes;
            $this->selling = ($this->fare + $this->taxes + $this->mark_up) * (1 + $serviceFee);
        } elseif ($oldAttributes['taxes'] != $this->taxes) {
            $this->net = $this->fare + $this->taxes;
            $this->selling = ($this->fare + $this->taxes + $this->mark_up) * (1 + $serviceFee);
        } elseif ($oldAttributes['mark_up'] != $this->mark_up) {
            $this->selling = ($this->fare + $this->taxes + $this->mark_up) * (1 + $serviceFee);
        } else {
            $this->oldParams = serialize($this->attributes);
            return $this;
        }

        $this->roundAttributesValue();
        $this->oldParams = serialize($this->attributes);
    }

    public function toFloat(&$attributes = null)
    {
        if ($attributes === null) {
            foreach ($this->attributes as $attr => $value) {
                if (in_array($attr, ['net', 'selling', 'mark_up', 'taxes', 'fare'])) {
                    $this->$attr = (float)str_replace(',', '', $value);
                }
            }
        } else {
            foreach ($attributes as $attr => $value) {
                if (in_array($attr, ['net', 'selling', 'mark_up', 'taxes', 'fare'])) {
                    $attributes[$attr] = (float)str_replace(',', '', $value);
                }
            }
        }
    }

    public function roundAttributesValue($precision = 2): void
    {
        foreach ($this->attributes as $attr => $value) {
            if (in_array($attr, ['net', 'selling', 'mark_up', 'taxes', 'fare'])) {
                $this->$attr = $this->roundValue($value, $precision);
            }

            if ($attr == 'miles') {
                $this->$attr = $this->roundValue($value, 0);
            }

            if ($attr == 'ppm') {
                $this->$attr = $this->roundValue($value, 4);
            }
        }
    }

    public function roundValue($value, ?int $precision = null): float
    {
        $precision = $precision ?? $this->defaultPrecision;
        return round((float)$value, $precision);
    }

    public function attributeLabels()
    {
        return [
            'ppm' => 'PPM'
        ];
    }
}

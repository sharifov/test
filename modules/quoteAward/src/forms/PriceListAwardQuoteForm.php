<?php

namespace modules\quoteAward\src\forms;

use common\models\QuotePrice;
use modules\quoteAward\src\dictionary\AwardProgramDictionary;
use modules\quoteAward\src\entities\QuoteFlightProgramQuery;
use yii\base\Model;

class PriceListAwardQuoteForm extends Model
{
    public $flight;
    public $passenger_type;
    public $passenger_count;
    public $selling = 0;
    public $net = 0;
    public $taxes = 0;
    public $fare = 0;
    public $mark_up = 0;

    public $is_required_award_program;
    public $award_program;
    public $miles = 0;
    public $ppm;
    public $oldParams;

    private int $defaultPrecision = 2;

    public function rules(): array
    {
        return [
            [['flight', 'passenger_type', 'selling', 'fare', 'net', 'taxes', 'mark_up', 'passenger_count', 'passenger_count', 'oldParams'], 'required'],
            ['net', 'number', 'min' => 1, 'tooSmall' => 'Net price must be greater than 0'],
            ['miles', 'integer'],
            ['ppm', 'safe'],
            ['award_program', 'in', 'range' => AwardProgramDictionary::geList()],
            ['passenger_type', 'in', 'range' => [QuotePrice::PASSENGER_ADULT, QuotePrice::PASSENGER_CHILD, QuotePrice::PASSENGER_INFANT]]
        ];
    }

    public function __construct(int $flight_id, string $passenger_type, int $passenger_count, bool $is_required_award_program = false, $config = [])
    {
        $this->flight = $flight_id;
        $this->passenger_type = $passenger_type;
        $this->passenger_count = $passenger_count;
        $this->is_required_award_program = $is_required_award_program;
        $this->oldParams = serialize($this->attributes);
        $this->ppm = QuoteFlightProgramQuery::getFirstProgramPpm();
        parent::__construct($config);
    }

    public function setParams(array $params)
    {
        $this->setAttributes($params);
    }

    public function calculatePrice()
    {
        $oldAttributes = unserialize($this->oldParams, ['allowed_classes' => false]);
        $this->oldParams = '';
        $this->toFloat();
        if ($oldAttributes['selling'] != $this->selling) {
            $this->mark_up = $this->selling - $this->net; // Selling Price/(1+SERVICE_FEE) - Net Price
        } elseif ($oldAttributes['fare'] != $this->fare) {
            $this->net = $this->fare + $this->taxes;
            if ($this->ppm > 0) {
                $this->miles = $this->fare / $this->ppm;
            }
            $this->selling = ($this->fare + $this->taxes + $this->mark_up); // Selling Price = (Fare + Taxes + Mark-up)*(1+SERVICE_FEE)
        } elseif ($oldAttributes['miles'] != $this->miles) {
            $this->fare = $this->miles * $this->ppm;
            $this->net = $this->fare + $this->taxes;
            $this->selling = ($this->fare + $this->taxes + $this->mark_up);
        } elseif ($oldAttributes['ppm'] != $this->ppm) {
            $this->fare = $this->miles * $this->ppm;
            $this->net = $this->fare + $this->taxes;
            $this->selling = ($this->fare + $this->taxes + $this->mark_up);
        } elseif ($oldAttributes['taxes'] != $this->taxes) {
            $this->net = $this->fare + $this->taxes;
            $this->selling = ($this->fare + $this->taxes + $this->mark_up);
        } elseif ($oldAttributes['mark_up'] != $this->mark_up) {
            $this->selling = ($this->fare + $this->taxes + $this->mark_up);
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
}

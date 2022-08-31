<?php

namespace modules\quoteAward\src\forms;

use common\models\Lead;
use common\models\QuotePrice;
use modules\flight\models\Flight;
use modules\quoteAward\src\dictionary\AwardProgramDictionary;
use modules\quoteAward\src\models\FlightAwardQuoteItem;
use src\forms\CompositeForm;

/**
 * @property PriceListAwardQuoteForm[] $prices
 */
class FlightAwardQuoteForm extends CompositeForm
{
    public $id;
    public $cabin;
    public $adults;
    public $children;
    public $infants;
    public $gds;
    public $validationCarrier;
    public $recordLocator;
    public $fareType;
    public $quoteProgram;

    /**
     * @param FlightAwardQuoteItem $flight
     * @param array $prices
     * @param array $config
     */
    public function __construct(FlightAwardQuoteItem $flight, array $prices = [], $config = [])
    {
        $this->attributes = $flight->attributes;

        $isRequiredAwardProgram = $flight->quoteProgram === AwardProgramDictionary::AWARD_MILE;

        $priceList[QuotePrice::PASSENGER_ADULT] = new PriceListAwardQuoteForm(
            (int)$flight->id,
            QuotePrice::PASSENGER_ADULT,
            (int)$this->adults,
            $isRequiredAwardProgram
        );

        if (!empty($prices) && array_key_exists($flight->id . '-' . QuotePrice::PASSENGER_ADULT, $prices)) {
            $priceList[QuotePrice::PASSENGER_ADULT]->setAttributes($prices[$flight->id . '-' . QuotePrice::PASSENGER_ADULT]);
        }

        if (((int)$this->children) > 0) {
            $priceList[QuotePrice::PASSENGER_CHILD] = new PriceListAwardQuoteForm(
                (int)$flight->id,
                QuotePrice::PASSENGER_CHILD,
                (int)$this->children,
                $isRequiredAwardProgram
            );

            if (!empty($prices) && array_key_exists($flight->id . '-' . QuotePrice::PASSENGER_CHILD, $prices)) {
                $priceList[QuotePrice::PASSENGER_CHILD]->setAttributes($prices[$flight->id . '-' . QuotePrice::PASSENGER_CHILD]);
            }
        }

        if (((int)$this->infants) > 0) {
            $priceList[QuotePrice::PASSENGER_INFANT] = new PriceListAwardQuoteForm(
                (int)$flight->id,
                QuotePrice::PASSENGER_INFANT,
                (int)$this->infants,
                $isRequiredAwardProgram
            );

            if (!empty($prices) && array_key_exists($flight->id . '-' . QuotePrice::PASSENGER_INFANT, $prices)) {
                $priceList[QuotePrice::PASSENGER_INFANT]->setAttributes($prices[$flight->id . '-' . QuotePrice::PASSENGER_INFANT]);
            }
        }

        $this->prices = $priceList;

        parent::__construct($config);
    }


    public function rules(): array
    {
        return [
            [['cabin', 'validationCarrier', 'cabin', 'gds', 'quoteProgram'], 'required'],
            ['cabin', 'string', 'max' => 1],
            ['id', 'safe'],
            ['cabin', 'in', 'range' => array_keys(Flight::getCabinClassList())],
            [['adults', 'children', 'infants'], 'required'],
            [['adults', 'children', 'infants'], 'integer', 'min' => 0, 'max' => 9],

            ['adults', function () {
                if (!$this->adults && !$this->children) {
                    $this->addError('adults', 'Adults or Children must be more 0.');
                }
            }],

            ['infants', function () {
                if ($this->infants > $this->adults) {
                    $this->addError('infants', 'Infants must be no greater than Adults');
                }
            }],
        ];
    }

    protected function internalForms(): array
    {
        return ['prices'];
    }
}

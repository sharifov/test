<?php

namespace modules\quoteAward\src\forms;

use common\models\Employee;
use common\models\Lead;
use common\models\QuotePrice;
use modules\flight\models\Flight;
use modules\quoteAward\src\dictionary\AwardProgramDictionary;
use modules\quoteAward\src\entities\QuoteFlightProgram;
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
    public $ppc;
    public $awardProgram;
    public $ppm;

    private $is_required_award_program;

    /**
     * @param FlightAwardQuoteItem $flight
     * @param array $prices
     * @param array $config
     */
    public function __construct(FlightAwardQuoteItem $flight, array $prices = [], $config = [])
    {
        $this->attributes = $flight->attributes;

        $priceList = [];
        $this->createPriceQuote($priceList, $prices, QuotePrice::PASSENGER_ADULT, $flight, 'adults');

        if (((int)$this->children) > 0) {
            $this->createPriceQuote($priceList, $prices, QuotePrice::PASSENGER_CHILD, $flight, 'children');
        }

        if (((int)$this->infants) > 0) {
            $this->createPriceQuote($priceList, $prices, QuotePrice::PASSENGER_INFANT, $flight, 'infants');
        }
        $this->is_required_award_program = ($flight->quoteProgram === AwardProgramDictionary::AWARD_MILE);

        $this->prices = $priceList;

        parent::__construct($config);
    }


    public function rules(): array
    {
        return [
            [['cabin', 'validationCarrier', 'gds', 'quoteProgram'], 'required'],
            ['cabin', 'string', 'max' => 1],
            [['ppc', 'fareType', 'recordLocator'], 'string'],
            ['id', 'integer'],
            ['ppm', 'number'],
            ['cabin', 'in', 'range' => array_keys(Flight::getCabinClassList())],
            ['awardProgram', 'in', 'range' => array_keys(QuoteFlightProgram::getList())],
            [['adults', 'children', 'infants'], 'required'],
            [['adults', 'children', 'infants'], 'integer', 'min' => 0, 'max' => 9],
            ['adults', 'integer', 'min' => 1, 'max' => 9],

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

    private function createPriceQuote(array &$priceList, array $prices, string $passenger_type, FlightAwardQuoteItem $flight, string $attribute_name)
    {
        $isRequiredAwardProgram = $flight->quoteProgram === AwardProgramDictionary::AWARD_MILE;

        $priceList[$passenger_type] = new PriceListAwardQuoteForm(
            $flight,
            $passenger_type,
            (int)$this->{$attribute_name},
            $isRequiredAwardProgram
        );

        if (!empty($prices) && array_key_exists($flight->id . '-' . $passenger_type, $prices)) {
            $priceList[$passenger_type]->setParams($prices[$flight->id . '-' . $passenger_type]);
        }
    }

    public function attributeLabels()
    {
        return [
            'ppc' => 'PPC'
        ];
    }

    public function isRequiredAwardProgram(): bool
    {
        return $this->is_required_award_program;
    }
}

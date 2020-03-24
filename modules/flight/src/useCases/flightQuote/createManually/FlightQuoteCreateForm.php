<?php
namespace modules\flight\src\useCases\flightQuote\createManually;

use common\models\Airline;
use common\models\Employee;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\src\helpers\FlightQuoteHelper;
use sales\forms\CompositeForm;

class FlightQuoteCreateForm extends CompositeForm
{
	public $recordLocator;

	public $gds;

	public $pcc;

	public $tripType;

	public $cabin;

	public $validatingCarrier;

	public $fareType;

	public $quoteCreator;

	public $reservationDump;

	public $pricingInfo;

	public $prices;

	public $itinerary = [];

	public function internalForms(): array
	{
		return ['prices'];
	}

	public function rules(): array
	{
		return [
			[['gds', 'pcc', 'tripType', 'cabin', 'validatingCarrier', 'fareType', 'reservationDump', 'pricingInfo'], 'string'],
			[['gds', 'validatingCarrier', 'cabin', 'tripType', 'fareType', 'reservationDump', 'quoteCreator'], 'required'],
			['quoteCreator', 'integer'],
			[['reservationDump', 'pricingInfo', 'prices'], 'safe'],
			[['reservationDump'], 'string'],
			[['quoteCreator'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['quoteCreator' => 'id']],
			['gds', 'in',  'range' => array_keys(FlightQuote::getGdsList())],
			['tripType', 'in',  'range' => array_keys(Flight::getTripTypeList())],
			['validatingCarrier', 'in',  'range' => array_keys(Airline::getAirlinesMapping(true))],
			['fareType', 'in',  'range' => array_keys(FlightQuote::getFareTypeList())],
			['cabin', 'in',  'range' => array_keys(Flight::getCabinClassList())],
			[['reservationDump'], 'checkReservationDump'],
		];
	}

	public function checkReservationDump(): void
	{
		$dumpParser = FlightQuoteHelper::parseDump($this->reservationDump, true, $this->itinerary);
		if (empty($dumpParser)) {
			$this->addError('reservationDump', 'Incorrect reservation dump!');
		}
	}
}
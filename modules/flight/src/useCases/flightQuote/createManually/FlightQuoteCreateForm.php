<?php
namespace modules\flight\src\useCases\flightQuote\createManually;

use common\models\Airline;
use common\models\Employee;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\flight\src\useCases\flightQuote\createManually\helpers\FlightQuotePaxPriceHelper;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethodQuery;
use sales\auth\Auth;
use sales\forms\CompositeForm;
use sales\helpers\product\ProductQuoteHelper;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteCreateForm
 * @package modules\flight\src\useCases\flightQuote\createManually
 *
 * @property FlightQuotePaxPriceForm[] $prices
 */
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

	public $itinerary = [];

	public $parsedPricingInfo = [];

	public $oldPrices;

	public $action;

	public $serviceFee;

	public $currencyRate;

	public $currencyCode;

	public $clientSelling;

	public const ACTION_APPLY_PRICING_INFO = 'apply_pricing';
	public const ACTION_CALCULATE_PRICES = 'calculate_prices';

	public function __construct(?Flight $flight = null, ?int $creatorId = null, $config = [])
	{
		if ($flight && $creatorId) {
			$this->tripType = $flight->fl_trip_type_id;
			$this->cabin = $flight->fl_cabin_class;
			$this->quoteCreator = $creatorId;
			$this->prices = FlightQuotePaxPriceHelper::getQuotePaxPriceFormCollection($flight);
			$this->oldPrices = serialize(ArrayHelper::toArray($this->prices));
			$this->serviceFee = ProductTypePaymentMethodQuery::getDefaultPercentFeeByProductType($flight->flProduct->pr_type_id) ?? (FlightQuote::SERVICE_FEE * 100);
			$this->currencyRate = ProductQuoteHelper::getClientCurrencyRate($flight->flProduct);
			$this->currencyCode = ProductQuoteHelper::getClientCurrencyCode($flight->flProduct);
		}
		parent::__construct($config);
	}

	public function afterValidate()
	{
		if (!empty($this->pricingInfo)) {
			$this->parsedPricingInfo = FlightQuoteHelper::parsePriceDump($this->pricingInfo);
		}
	}

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
			[['reservationDump', 'pricingInfo', 'prices', 'recordLocator', 'pricingInfo', 'action', 'oldPrices', 'serviceFee'], 'safe'],
			[['reservationDump', 'oldPrices'], 'string'],
			['recordLocator', 'string', 'length' => 8],
			['pcc', 'string', 'length' => 10],
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

	public function updateDataByPricingDump(array $dump): void
	{
		if (!empty($dump['validating_carrier'])) {
			$this->validatingCarrier = $dump['validating_carrier'];
		}

		if (!empty($dump['prices']))
		{
			foreach ($this->prices as $price) {
				foreach ($dump['prices'] as $paxType => $dumpPrice) {
					if ($price->paxCode === $paxType) {
						$price->fare = $dumpPrice['fare'] ?? $price->fare;
						$price->taxes = $dumpPrice['taxes'] ?? $price->taxes;
					}
				}
			}
		}
	}
}
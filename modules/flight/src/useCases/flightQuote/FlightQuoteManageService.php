<?php


namespace modules\flight\src\useCases\flightQuote;


use modules\flight\models\FlightQuoteStatusLog;
use modules\flight\src\repositories\flightQuoteStatusLogRepository\FlightQuoteStatusLogRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteSegmentPaxBaggage;
use modules\flight\models\FlightQuoteSegmentPaxBaggageCharge;
use modules\flight\models\FlightQuoteSegmentStop;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\flight\src\repositories\flightPaxRepository\FlightPaxRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\FlightQuoteSegmentPaxBaggageChargeRepository\FlightQuoteSegmentPaxBaggageChargeRepository;
use modules\flight\src\repositories\flightQuoteSegmentPaxBaggageRepository\FlightQuoteSegmentPaxBaggageRepository;
use modules\flight\src\repositories\flightQuoteSegmentStopRepository\FlightQuoteSegmentStopRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteCreateDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuotePaxPriceDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentPaxBaggageChargeDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentPaxBaggageDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentStopDTO;
use modules\flight\src\useCases\flightQuote\create\ProductQuoteCreateDTO;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;

/**
 * Class FlightQuoteManageService
 * @package modules\flight\src\useCases\flightQuote
 *
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property TransactionManager $transactionManager
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuoteTripRepository $flightQuoteTripRepository
 * @property FlightQuoteSegmentRepository $flightQuoteSegmentRepository
 * @property FlightQuoteSegmentStopRepository $flightQuoteSegmentStopRepository
 * @property FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository
 * @property FlightPaxRepository $flightPaxRepository
 * @property FlightQuoteSegmentPaxBaggageChargeRepository $baggageChargeRepository
 * @property FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
 * @property FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository
 */
class FlightQuoteManageService
{
	/**
	 * @var FlightQuoteRepository
	 */
	private $flightQuoteRepository;
	/**
	 * @var TransactionManager
	 */
	private $transactionManager;
	/**
	 * @var ProductQuoteRepository
	 */
	private $productQuoteRepository;
	/**
	 * @var FlightQuoteTripRepository
	 */
	private $flightQuoteTripRepository;
	/**
	 * @var FlightQuoteSegmentRepository
	 */
	private $flightQuoteSegmentRepository;
	/**
	 * @var FlightQuoteSegmentStopRepository
	 */
	private $flightQuoteSegmentStopRepository;
	/**
	 * @var FlightQuoteSegmentPaxBaggageRepository
	 */
	private $flightQuoteSegmentPaxBaggageRepository;
	/**
	 * @var FlightPaxRepository
	 */
	private $flightPaxRepository;
	/**
	 * @var FlightQuoteSegmentPaxBaggageChargeRepository
	 */
	private $baggageChargeRepository;
	/**
	 * @var FlightQuotePaxPriceRepository
	 */
	private $flightQuotePaxPriceRepository;
	/**
	 * @var FlightQuoteStatusLogRepository
	 */
	private $flightQuoteStatusLogRepository;

	/**
	 * FlightQuoteService constructor.
	 * @param FlightQuoteRepository $flightQuoteRepository
	 * @param ProductQuoteRepository $productQuoteRepository
	 * @param FlightPaxRepository $flightPaxRepository
	 * @param FlightQuoteTripRepository $flightQuoteTripRepository
	 * @param FlightQuoteSegmentRepository $flightQuoteSegmentRepository
	 * @param FlightQuoteSegmentStopRepository $flightQuoteSegmentStopRepository
	 * @param FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository
	 * @param FlightQuoteSegmentPaxBaggageChargeRepository $baggageChargeRepository
	 * @param FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
	 * @param FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository
	 * @param TransactionManager $transactionManager
	 */
	public function __construct(
		FlightQuoteRepository $flightQuoteRepository,
		ProductQuoteRepository $productQuoteRepository,
		FlightPaxRepository $flightPaxRepository,
		FlightQuoteTripRepository $flightQuoteTripRepository,
		FlightQuoteSegmentRepository $flightQuoteSegmentRepository,
		FlightQuoteSegmentStopRepository $flightQuoteSegmentStopRepository,
		FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository,
		FlightQuoteSegmentPaxBaggageChargeRepository $baggageChargeRepository,
		FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository,
		FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository,
		TransactionManager $transactionManager)
	{
		$this->flightQuoteRepository = $flightQuoteRepository;
		$this->productQuoteRepository = $productQuoteRepository;
		$this->flightPaxRepository = $flightPaxRepository;
		$this->flightQuoteTripRepository = $flightQuoteTripRepository;
		$this->flightQuoteSegmentRepository = $flightQuoteSegmentRepository;
		$this->flightQuoteSegmentStopRepository = $flightQuoteSegmentStopRepository;
		$this->flightQuoteSegmentPaxBaggageRepository = $flightQuoteSegmentPaxBaggageRepository;
		$this->baggageChargeRepository = $baggageChargeRepository;
		$this->flightQuotePaxPriceRepository = $flightQuotePaxPriceRepository;
		$this->flightQuoteStatusLogRepository = $flightQuoteStatusLogRepository;
		$this->transactionManager = $transactionManager;
	}

	/**
	 * @param Flight $flight
	 * @param array $quote
	 * @param int $userId
	 * @throws \Throwable
	 */
	public function create(Flight $flight, array $quote, int $userId): void
	{
		$this->transactionManager->wrap(function () use ($flight, $quote, $userId) {
			$productQuote = ProductQuote::create((new ProductQuoteCreateDTO($flight, $quote, $userId)));
			$this->productQuoteRepository->save($productQuote);

			$flightQuote = FlightQuote::create((new FlightQuoteCreateDTO($flight, $productQuote, $quote, $userId)));
			$this->flightQuoteRepository->save($flightQuote);

			$flightQuoteLog = FlightQuoteStatusLog::create($flightQuote->fq_created_user_id, $flightQuote->fq_id, $productQuote->pq_status_id);
			$this->flightQuoteStatusLogRepository->save($flightQuoteLog);

			$this->createQuotePaxPrice($flightQuote, $productQuote, $quote);

			$this->createFlightTrip($flightQuote, $quote);
		});
	}

	/**
	 * @param FlightQuote $flightQuote
	 * @param ProductQuote $productQuote
	 * @param array $quote
	 */
	private function createQuotePaxPrice(FlightQuote $flightQuote, ProductQuote $productQuote, array $quote): void
	{
		foreach ($quote['passengers'] as $passengerType => $passenger) {
			$flightQuotePaxPrice = FlightQuotePaxPrice::create((new FlightQuotePaxPriceDTO($flightQuote, $productQuote, $passenger, $passengerType, $quote)));
			$this->flightQuotePaxPriceRepository->save($flightQuotePaxPrice);
		}
	}

	/**
	 * @param FlightQuote $flightQuote
	 * @param array $quote
	 */
	private function createFlightTrip(FlightQuote $flightQuote, array $quote): void
	{
		foreach ($quote['trips'] as $tripKey => $trip) {
			$tripNr = (int)$tripKey + 1;
			$segmentNr = 1;

			$flightTrip = FlightQuoteTrip::create($flightQuote, $quote['totalDuration']);
			$this->flightQuoteTripRepository->save($flightTrip);

			$this->createSegment($trip, $flightQuote, $flightTrip, $tripNr, $segmentNr);
		}
	}

	/**
	 * @param array $trip
	 * @param FlightQuote $flightQuote
	 * @param FlightQuoteTrip $flightQuoteTrip
	 * @param int $tripNr
	 * @param int $segmentNr
	 */
	private function createSegment(array $trip, FlightQuote $flightQuote, FlightQuoteTrip $flightQuoteTrip, int $tripNr, int $segmentNr): void
	{
		foreach ($trip['segments'] as $segment) {

			$ticketId = FlightQuoteHelper::getTicketId($flightQuote, $tripNr, $segmentNr);

			$flightQuoteSegment = FlightQuoteSegment::create((new FlightQuoteSegmentDTO($flightQuote, $flightQuoteTrip, $segment, $ticketId)));
			$this->flightQuoteSegmentRepository->save($flightQuoteSegment);

			if (!empty($segment['stops'])) {
				$this->createQuoteSegmentStop($flightQuoteSegment, $segment);
			}

			if (!empty($segment['baggage'])) {
				$this->createQuoteSegmentPaxBaggage($flightQuoteSegment, $segment);
			}

			$segmentNr++;
		}
	}

	/**
	 * @param FlightQuoteSegment $flightQuoteSegment
	 * @param array $segment
	 */
	private function createQuoteSegmentStop(FlightQuoteSegment $flightQuoteSegment, array $segment): void
	{
		foreach ($segment['stops'] as $stop) {
			$flightQuoteSegmentStop = FlightQuoteSegmentStop::create((new FlightQuoteSegmentStopDTO($flightQuoteSegment, $stop)));
			$this->flightQuoteSegmentStopRepository->save($flightQuoteSegmentStop);
		}
	}

	/**
	 * @param FlightQuoteSegment $flightQuoteSegment
	 * @param array $segment
	 */
	private function createQuoteSegmentPaxBaggage(FlightQuoteSegment $flightQuoteSegment, array $segment): void
	{
		foreach ($segment['baggage'] as $paxType => $baggage) {
			$flightQuoteSegmentPaxBaggage = FlightQuoteSegmentPaxBaggage::create((new FlightQuoteSegmentPaxBaggageDTO($flightQuoteSegment, $paxType, $baggage)));
			$this->flightQuoteSegmentPaxBaggageRepository->save($flightQuoteSegmentPaxBaggage);

			if (!empty($baggage['charge'])) {
				$this->createQuoteSegmentPaxBaggageCharge($flightQuoteSegment, $paxType, $baggage);
			}
		}
	}

	/**
	 * @param FlightQuoteSegment $flightQuoteSegment
	 * @param string $paxType
	 * @param array $baggage
	 */
	private function createQuoteSegmentPaxBaggageCharge(FlightQuoteSegment $flightQuoteSegment, string $paxType, array $baggage): void
	{
		foreach ($baggage['charge'] as $charge) {
			$paxBaggageCharge = FlightQuoteSegmentPaxBaggageCharge::create((new FlightQuoteSegmentPaxBaggageChargeDTO($flightQuoteSegment, $paxType, $charge)));
			$this->baggageChargeRepository->save($paxBaggageCharge);
		}
	}
}
<?php

namespace modules\flight\src\useCases\reProtectionQuoteManualCreate\service;

use common\components\SearchService;
use common\models\QuoteSegment;
use common\models\QuoteSegmentBaggage;
use common\models\QuoteSegmentBaggageCharge;
use DateTime;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteSegmentPaxBaggage;
use modules\flight\models\FlightQuoteSegmentPaxBaggageCharge;
use modules\flight\models\FlightQuoteStatusLog;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\models\FlightSegment;
use modules\flight\src\dto\flightSegment\SegmentDTO;
use modules\flight\src\dto\itineraryDump\ItineraryDumpDTO;
use modules\flight\src\repositories\flightQuoteBooking\FlightQuoteBookingRepository;
use modules\flight\src\repositories\flightQuoteFlight\FlightQuoteFlightRepository;
use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\FlightQuoteSegmentPaxBaggageChargeRepository\FlightQuoteSegmentPaxBaggageChargeRepository;
use modules\flight\src\repositories\flightQuoteSegmentPaxBaggageRepository\FlightQuoteSegmentPaxBaggageRepository;
use modules\flight\src\repositories\flightQuoteStatusLogRepository\FlightQuoteStatusLogRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;
use modules\flight\src\repositories\flightSegment\FlightSegmentRepository;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteCreateDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentDTOItinerary;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\reProtectionQuoteManualCreate\form\ReProtectionQuoteCreateForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteData\service\ProductQuoteDataManageService;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\repositories\ProductQuoteRelationRepository;
use sales\auth\Auth;
use sales\forms\segment\SegmentBaggageForm;
use sales\helpers\ErrorsToStringHelper;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\parsingDump\BaggageService;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * Class ReProtectionQuoteManualCreateService
 *
 * @property FlightQuoteFlightRepository $flightQuoteFlightRepository
 * @property FlightQuoteBookingRepository $flightQuoteBookingRepository
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository
 * @property FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository
 * @property FlightQuoteTripRepository $flightQuoteTripRepository
 * @property FlightSegmentRepository $flightSegmentRepository
 * @property FlightQuoteSegmentRepository $flightQuoteSegmentRepository
 * @property FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository
 * @property FlightQuoteSegmentPaxBaggageChargeRepository $flightQuoteSegmentPaxBaggageChargeRepository
 * @property FlightQuoteManageService $flightQuoteManageService
 * @property ProductQuoteRelationRepository $productQuoteRelationRepository
 * @property ProductQuoteOptionRepository $productQuoteOptionRepository
 * @property ProductQuoteDataManageService $productQuoteDataManageService
 */
class ReProtectionQuoteManualCreateService
{
    private FlightQuoteFlightRepository $flightQuoteFlightRepository;
    private FlightQuoteBookingRepository $flightQuoteBookingRepository;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private ProductQuoteRepository $productQuoteRepository;
    private FlightQuoteRepository $flightQuoteRepository;
    private FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository;
    private FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository;
    private FlightSegmentRepository $flightSegmentRepository;
    private FlightQuoteSegmentRepository $flightQuoteSegmentRepository;
    private FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository;
    private FlightQuoteSegmentPaxBaggageChargeRepository $flightQuoteSegmentPaxBaggageChargeRepository;
    private FlightQuoteManageService $flightQuoteManageService;
    private ProductQuoteRelationRepository $productQuoteRelationRepository;
    private ProductQuoteOptionRepository $productQuoteOptionRepository;
    private ProductQuoteDataManageService $productQuoteDataManageService;

    public function __construct(
        FlightQuoteFlightRepository $flightQuoteFlightRepository,
        FlightQuoteBookingRepository $flightQuoteBookingRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        ProductQuoteRepository $productQuoteRepository,
        FlightQuoteRepository $flightQuoteRepository,
        FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository,
        FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository,
        FlightQuoteTripRepository $flightQuoteTripRepository,
        FlightSegmentRepository $flightSegmentRepository,
        FlightQuoteSegmentRepository $flightQuoteSegmentRepository,
        FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository,
        FlightQuoteSegmentPaxBaggageChargeRepository $flightQuoteSegmentPaxBaggageChargeRepository,
        FlightQuoteManageService $flightQuoteManageService,
        ProductQuoteRelationRepository $productQuoteRelationRepository,
        ProductQuoteOptionRepository $productQuoteOptionRepository,
        ProductQuoteDataManageService $productQuoteDataManageService
    ) {
        $this->flightQuoteFlightRepository = $flightQuoteFlightRepository;
        $this->flightQuoteBookingRepository = $flightQuoteBookingRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->flightQuoteStatusLogRepository = $flightQuoteStatusLogRepository;
        $this->flightQuotePaxPriceRepository = $flightQuotePaxPriceRepository;
        $this->flightQuoteTripRepository = $flightQuoteTripRepository;
        $this->flightSegmentRepository = $flightSegmentRepository;
        $this->flightQuoteSegmentRepository = $flightQuoteSegmentRepository;
        $this->flightQuoteSegmentPaxBaggageRepository = $flightQuoteSegmentPaxBaggageRepository;
        $this->flightQuoteSegmentPaxBaggageChargeRepository = $flightQuoteSegmentPaxBaggageChargeRepository;
        $this->flightQuoteManageService = $flightQuoteManageService;
        $this->productQuoteRelationRepository = $productQuoteRelationRepository;
        $this->productQuoteOptionRepository = $productQuoteOptionRepository;
        $this->productQuoteDataManageService = $productQuoteDataManageService;
    }

    public function createReProtectionManual(
        Flight $flight,
        ProductQuote $originProductQuote,
        ReProtectionQuoteCreateForm $form,
        ?int $userId,
        array $segments
    ): FlightQuote {
        if ($reprotectionQuotes = ProductQuoteQuery::getReprotectionQuotesByOriginQuote($originProductQuote->pq_id)) {
            foreach ($reprotectionQuotes as $reprotectionQuote) {
                if ($reprotectionQuote->pq_owner_user_id === null && (!$reprotectionQuote->isCanceled() || $reprotectionQuote->isDeclined() || !$reprotectionQuote->isError())) {
                    $reprotectionQuote->declined($userId, 'Create ReProtection Quote');
                    $this->productQuoteRepository->save($reprotectionQuote);
                }
            }
        }

        $originFlightQuote = $originProductQuote->flightQuote;
        $productQuote = $this->copyOriginalProductQuote($originProductQuote, $form->quoteCreator, $form->quoteCreator);

        $quoteData = self::prepareFlightQuoteData($form);
        $flightQuoteCreateDTO = FlightQuoteCreateDTO::fillReProtectionManual($flight, $productQuote, $quoteData, Auth::id(), $form);
        $flightQuote = FlightQuote::createReProtectionManual($flightQuoteCreateDTO);
        $this->flightQuoteRepository->save($flightQuote);

        $flightQuoteLog = FlightQuoteStatusLog::create($flightQuote->fq_created_user_id, $flightQuote->fq_id, $productQuote->pq_status_id);
        $this->flightQuoteStatusLogRepository->save($flightQuoteLog);

        foreach ($originFlightQuote->flightQuotePaxPrices as $originalPaxPrice) {
            $paxPrice = FlightQuotePaxPrice::clone($originalPaxPrice, $flightQuote->fq_id);
            $this->flightQuotePaxPriceRepository->save($paxPrice);
        }

        $this->flightQuoteManageService->createFlightQuoteFlight($flightQuote, null);
        $relation = ProductQuoteRelation::createReProtection($originProductQuote->pq_id, $flightQuote->fq_product_quote_id, $userId);
        $this->productQuoteRelationRepository->save($relation);

        $tripList = explode(',', $form->keyTripList);
        $tripList = array_combine(array_values($tripList), array_values($tripList));
        $segmentTripMap = [];
        foreach ($tripList as $tripKey => $value) {
            $flightQuoteTrip = FlightQuoteTrip::create($flightQuote, null);
            $this->flightQuoteTripRepository->save($flightQuoteTrip);
            $tripList[$tripKey] = $flightQuoteTrip;
        }
        foreach ($form->getSegmentTripFormsData() as $keyTripForm => $value) {
            if (!is_array($value) || !array_key_exists('segment_iata', $value) || !array_key_exists('segment_trip_key', $value)) {
                continue;
            }
            $segmentTripMap[$value['segment_iata']] = $value['segment_trip_key'];
        }
        foreach ($tripList as $tripKey => $flightQuoteTrip) {/* TODO: tmp solution */
            if (!in_array($tripKey, $segmentTripMap, false)) {
                $flightQuoteTrip->delete();
            }
        }

        $flightQuoteSegments = [];
        foreach ($form->itinerary as $key => $itinerary) {
            $keyIata = $itinerary->departureAirportCode . $itinerary->arrivalAirportCode;

            if ((!$tripKey = $segmentTripMap[$keyIata] ?? null) || !$flightQuoteTrip = $tripList[$tripKey] ?? null) {
                throw new \DomainException('Segment (' . $keyIata . ') not found in TripList');
            }

            $flightQuoteTripId = $flightQuoteTrip->fqt_id;
            $segmentDto = new FlightQuoteSegmentDTOItinerary($flightQuote->getId(), $flightQuoteTripId, $itinerary);
            $flightQuoteSegment = FlightQuoteSegment::create($segmentDto);

            if ($form->gds === SearchService::GDS_WORLDSPAN) {
                $flightQuoteSegment = self::postProcessingWordspan($flightQuoteSegment, $segments);
            }

            $this->flightQuoteSegmentRepository->save($flightQuoteSegment);
            $keyIata = $flightQuoteSegment->fqs_departure_airport_iata . $flightQuoteSegment->fqs_arrival_airport_iata;
            $flightQuoteSegments[$keyIata] = $flightQuoteSegment;

            $flightQuoteTrip->fqt_duration += $flightQuoteSegment->fqs_duration;
            $flightQuoteTrip->update(false, ['fqt_duration']);
        }

        if ($form->getBaggageFormsData()) {
            foreach ($form->getBaggageFormsData() as $postKey => $postValues) {
                $patternBaggageForm = '/SegmentBaggageForm_([A-Z]{3})([A-Z]{3})\z/';
                preg_match($patternBaggageForm, $postKey, $iataMatches);
                if (!isset($iataMatches[2])) {
                    continue;
                }
                $keyIata = $iataMatches[1] . $iataMatches[2];
                /** @var FlightQuoteSegment $segment */
                if (
                    (isset($postValues['baggageData']) && is_array($postValues['baggageData'])) &&
                    $segment = $flightQuoteSegments[$keyIata] ?? null
                ) {
                    $firstPiece = $lastPiece = 0;

                    foreach ($postValues['baggageData'] as $key => $baggageData) {
                        $segmentBaggageForm = new SegmentBaggageForm();
                        $segmentBaggageForm->segmentId = $segment->fqs_id;
                        $segmentBaggageForm->load($baggageData, '');

                        if ($segmentBaggageForm->validate()) {
                            if ($segmentBaggageForm->type === BaggageService::TYPE_PAID) {
                                if ($segmentBaggageForm->piece === 1) {
                                    ++$firstPiece;
                                    $lastPiece = $firstPiece;
                                } else {
                                    $firstPiece = $lastPiece + 1;
                                    $lastPiece = $firstPiece + $segmentBaggageForm->piece - 1;
                                }
                                $flightQuoteSegmentPaxBaggageCharge = FlightQuoteSegmentPaxBaggageCharge::createByParams(
                                    FlightPax::getPaxId($segmentBaggageForm->paxCode),
                                    $segment->fqs_id,
                                    $firstPiece,
                                    $lastPiece,
                                    $segmentBaggageForm->price,
                                    $segmentBaggageForm->currency,
                                    $segmentBaggageForm->price,
                                    $segmentBaggageForm->price,
                                    $segmentBaggageForm->currency,
                                    $segmentBaggageForm->weight,
                                    $segmentBaggageForm->height
                                );
                                $this->flightQuoteSegmentPaxBaggageChargeRepository->save($flightQuoteSegmentPaxBaggageCharge);
                            } else {
                                $flightQuoteSegmentPaxBaggage = FlightQuoteSegmentPaxBaggage::createByParams(
                                    FlightPax::getPaxId($segmentBaggageForm->paxCode),
                                    $segment->fqs_id,
                                    null,
                                    $segment->fqs_operating_airline,
                                    $segmentBaggageForm->piece,
                                    null,
                                    null,
                                    $segmentBaggageForm->weight,
                                    $segmentBaggageForm->height
                                );
                                $this->flightQuoteSegmentPaxBaggageRepository->save($flightQuoteSegmentPaxBaggage);
                            }
                        }
                    }
                }
            }
        }

        $this->productQuoteDataManageService->updateRecommendedReprotectionQuote($originProductQuote->pq_id, $flightQuote->fq_product_quote_id);

        return $flightQuote;
    }

    private static function postProcessingWordspan(FlightQuoteSegment $flightQuoteSegment, array $segments): FlightQuoteSegment /* TODO:: tmp solution */
    {
        $keyIata = $flightQuoteSegment->fqs_departure_airport_iata . $flightQuoteSegment->fqs_arrival_airport_iata;
        $keySegment = array_search($keyIata, array_column($segments, 'segmentIata'), false);

        if ($keySegment !== false) {
            $departure = $segments[$keySegment]['departureDateTime'];
            $departureUtc = clone($departure);
            $departureUtc = $departureUtc->setTimezone(new \DateTimeZone('UTC'));

            $arrival = $segments[$keySegment]['arrivalDateTime'];
            $arrivalUtc = clone($arrival);
            $arrivalUtc = $arrivalUtc->setTimezone(new \DateTimeZone('UTC'));

            if ($arrivalUtc < $departureUtc) {
                $arrivalUtc = $arrivalUtc->modify('+1 days');
            }

            $flightQuoteSegment->fqs_departure_dt = $departureUtc->format('Y-m-d H:i:s');
            $flightQuoteSegment->fqs_arrival_dt = $arrivalUtc->format('Y-m-d H:i:s');
            $flightQuoteSegment->fqs_duration = ($arrivalUtc->getTimestamp() - $departureUtc->getTimestamp()) / 60;
        }

        return $flightQuoteSegment;
    }

    public static function isMoreOneDay(\DateTime $departureDateTime, \DateTime $arrivalDateTime): bool
    {
        $diff = $departureDateTime->diff($arrivalDateTime);
        return (int) sprintf('%d%d%d', $diff->y, $diff->m, $diff->d) >= 1;
    }

    private function copyOriginalProductQuote(ProductQuote $originalQuote, ?int $ownerId, ?int $creatorId): ProductQuote
    {
        $productQuote = ProductQuote::copy($originalQuote, $ownerId, $creatorId);
        $this->productQuoteRepository->save($productQuote);

        foreach ($originalQuote->productQuoteOptions as $originalProductQuoteOption) {
            $productQuoteOption = ProductQuoteOption::copy($originalProductQuoteOption, $productQuote->pq_id);
            $this->productQuoteOptionRepository->save($productQuoteOption);
        }

        return $productQuote;
    }

    public static function getOriginQuoteByFlight(int $flightId): ?ProductQuote
    {
        return ProductQuote::find()
            ->select(ProductQuote::tableName() . '.*')
            ->innerJoin(FlightQuote::tableName(), 'fq_product_quote_id = pq_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fq_id = fqf_fq_id')
            ->where(['fq_flight_id' => $flightId])
            ->andWhere(['IS NOT', 'fqf_booking_id', null])
            ->orderBy(['pq_id' => SORT_DESC])
            ->one();
    }

    public static function getOriginQuoteByFlightOld(int $flightId): ?ProductQuote
    {
        return ProductQuote::find()
            ->from(Flight::tableName())
            ->select(ProductQuote::tableName() . '.*')
            ->innerJoin(FlightQuote::tableName(), 'fq_flight_id = fl_id')
            ->innerJoin(ProductQuote::tableName(), 'fq_product_quote_id = pq_id')
            ->innerJoin(Product::tableName(), 'pq_product_id = pr_id AND pr_type_id = ' . ProductType::PRODUCT_FLIGHT)
            ->leftJoin(ProductQuoteRelation::tableName(), 'pqr_related_pq_id = pq_id AND pqr_type_id = ' . ProductQuoteRelation::TYPE_REPROTECTION)
            ->where(['fl_id' => $flightId])
            ->andWhere(['IS', 'pqr_related_pq_id', null])
            ->orderBy(['pq_id' => SORT_DESC])
            ->one();
    }

    private static function prepareFlightQuoteData(ReProtectionQuoteCreateForm $form): array
    {
        return [
            'recordLocator' => $form->recordLocator,
            'gds' => $form->gds,
            'pcc' => $form->pcc,
            'validatingCarrier' => $form->validatingCarrier,
            'fareType' => $form->fareType,
            'key' => md5(serialize($form->toArray())),
            'trips' => [], /* TODO::  */
        ];
    }
}

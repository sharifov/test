<?php

namespace src\services\cases;

use common\components\BackOffice;
use common\helpers\LogHelper;
use common\models\CaseSale;
use common\models\Project;
use Exception;
use frontend\helpers\JsonHelper;
use frontend\models\form\CreditCardForm;
use modules\flight\src\useCases\sale\FlightFromSaleService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use modules\order\src\services\OrderManageService;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\exception\BoResponseException;
use src\forms\caseSale\CaseSaleRequestBoForm;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\helpers\setting\SettingHelper;
use src\model\saleTicket\useCase\create\SaleTicketService;
use src\repositories\cases\CasesRepository;
use src\repositories\cases\CasesSaleRepository;
use Yii;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;

/**
 * Class CasesSaleService
 *
 * @property CasesSaleRepository $casesSaleRepository
 * @property SaleTicketService $saleTicketService
 * @property OrderCreateFromSaleService $orderCreateFromSaleService
 * @property OrderRepository $orderRepository
 * @property FlightFromSaleService $flightFromSaleService
 */
class CasesSaleService
{
    public const SENSITIVE_KEYS = [
        'firstName' => 'firstName', 'lastName' => 'lastName', 'phoneNumber' => 'phoneNumber',
        'first_name' => 'first_name', 'middle_name' => 'middle_name', 'last_name' => 'last_name',
        'phone' => 'phone', 'email' => 'email', 'projectApiKey' => 'projectApiKey', 'ticket_number' => 'ticket_number',
    ];

    private const FORMAT_PASSENGERS_DATA = [
        'meal' => 'formatByCountSegments',
        'wheelchair' => 'formatByCountSegments',
        'ff_numbers' => 'formatByFFAirline',
        'kt_numbers' => 'formatByAirline',
    ];

    /**
     * @var CasesSaleRepository
     */
    private $casesSaleRepository;

    /**
     * @var array
     */
    private $segments = [];

    /**
     * @var string
     */
    private $validatingCarrier;

    /**
     * @var array
     */
    private $namref = [];

    /**
     * @var CasesRepository
     */
    private $casesRepository;

    private SaleTicketService $saleTicketService;
    private OrderCreateFromSaleService $orderCreateFromSaleService;
    private OrderRepository $orderRepository;
    private FlightFromSaleService $flightFromSaleService;

    public function __construct(
        CasesSaleRepository $casesSaleRepository,
        SaleTicketService $saleTicketService,
        OrderCreateFromSaleService $orderCreateFromSaleService,
        OrderRepository $orderRepository,
        FlightFromSaleService $flightFromSaleService,
        CasesRepository $casesRepository
    ) {
        $this->casesSaleRepository = $casesSaleRepository;
        $this->saleTicketService = $saleTicketService;
        $this->orderCreateFromSaleService = $orderCreateFromSaleService;
        $this->orderRepository = $orderRepository;
        $this->flightFromSaleService = $flightFromSaleService;
        $this->casesRepository = $casesRepository;
    }

    /**
     * @param CaseSale $caseSale
     * @return array
     * @throws Exception
     */
    public function prepareSaleData(CaseSale $caseSale): array
    {
        $originalData = JsonHelper::decode($caseSale->css_sale_data);
        $updatedData = JsonHelper::decode($caseSale->css_sale_data_updated);

        $difference = $this->compareSaleData($originalData, $updatedData);

        if (empty($originalData['passengers'])) {
            throw new \RuntimeException('Sale Info: not found passengers data while preparing data for sync with B/0', 10);
        }

        $this->bufferPassengerNameref($originalData['passengers'])->preparePassengersData($difference);

        return $difference;
    }

    /**
     * @param CaseSale $caseSale
     * @return array
     */
    public function getSegments(CaseSale $caseSale): array
    {
        $updatedData = JsonHelper::decode($caseSale->css_sale_data_updated);

        $segments = [];

        foreach ($updatedData['itinerary'] as $itinerary) {
            foreach ($itinerary['segments'] as $segment) {
                $segments[] = $segment;
            }
        }

        return $segments;
    }

    /**
     * @param CaseSale $caseSale
     * @return CasesSaleService
     */
    public function setSegments(CaseSale $caseSale): CasesSaleService
    {
        $this->segments = $this->getSegments($caseSale);

        return $this;
    }

    /**
     * @param CaseSale $caseSale
     * @return CasesSaleService
     */
    public function setValidatingCarrier(CaseSale $caseSale): CasesSaleService
    {
        $updatedData = JsonHelper::decode($caseSale->css_sale_data_updated);

        $this->validatingCarrier = $updatedData['validatingCarrier'];

        return $this;
    }

    /**
     * @param CaseSale $caseSale
     * @return bool
     */
    public function isDataBackedUpToOriginal(CaseSale $caseSale): bool
    {
        $oldData = JsonHelper::decode($caseSale->css_sale_data);
        $newData = JsonHelper::decode($caseSale->css_sale_data_updated);

        $difference = $this->compareSaleData($oldData, $newData);

        return !$difference ? true : false;
    }

    /**
     * @param array $passengers
     * @return bool
     */
    public function checkIfPassengersHasNamerefAttribute(array $passengers): bool
    {
        foreach ($passengers as $passenger) {
            if (!empty($passenger['nameref'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $saleDataDiff
     * @throws Exception
     */
    private function preparePassengersData(array &$saleDataDiff): void
    {
        if (isset($saleDataDiff['passengers']) && !empty($this->namref)) {
            foreach ($saleDataDiff['passengers'] as $key => $passenger) {
                $this->formatFFNumbersByAirline($passenger, $saleDataDiff, $key);

                unset($saleDataDiff['passengers'][$key]);
                $saleDataDiff['passengers'][$this->namref[$key]] = $passenger;
            }
        } else {
            throw new \RuntimeException('Sale info doesnt have passengers or passengers nameref');
        }
    }

    /**
     * @param array $passenger
     * @param array $saleDataDiff
     * @param string $key
     */
    private function formatFFNumbersByAirline(array &$passenger, array $saleDataDiff, string $key): void
    {
        if (empty($passenger['ff_numbers']) && !empty($passenger['ff_airline'])) {
            throw new \RuntimeException('Cant send data to B/O, Frequent Flyer is not provided;', -1);
        }

        if (array_key_first($passenger) === 'ff_numbers' && !empty($saleDataDiff['passengers'][$key]['ff_airline'])) {
            $passenger['ff_numbers'] = [
                $passenger['ff_airline'] => array_shift($passenger['ff_numbers'])
            ];
            unset($passenger['ff_airline']);
        }
    }

    /**
     * @param array $passenger
     */
    public function formatPassengersData(array &$passenger): void
    {
        foreach ($passenger as $key => $value) {
            if (array_key_exists($key, self::FORMAT_PASSENGERS_DATA) && method_exists($this, self::FORMAT_PASSENGERS_DATA[$key])) {
                $this->{self::FORMAT_PASSENGERS_DATA[$key]}($passenger, $key);
            }
        }
    }

    /**
     * @param array $passenger
     * @param string $key
     */
    private function formatByCountSegments(array &$passenger, string $key): void
    {
        $value = $passenger[$key];
        $passenger[$key] = [];
        foreach ($this->segments as $segmentKey => $segment) {
            $passenger[$key][$segmentKey + 1] = $value;
        }
    }

    /**
     * @param array $passenger
     * @param string $key
     */
    private function formatByFFAirline(array &$passenger, string $key): void
    {
        $value = $passenger[$key];
        $passenger = [];
        $passenger[$key][$this->validatingCarrier] = $value;
    }

    /**
     * @param array $passenger
     * @param string $key
     */
    private function formatByAirline(array &$passenger, string $key): void
    {
        $value = $passenger[$key];
        $passenger[$key] = [];
        foreach ($this->segments as $segmentKey => $segment) {
            $passenger[$key][$segment['airline']] = $value;
        }
    }

    /**
     * @param array $passengers
     * @return CasesSaleService
     */
    private function bufferPassengerNameref(array $passengers): CasesSaleService
    {
        foreach ($passengers as $key => $passenger) {
            if (empty($passenger['nameref'])) {
                throw new \RuntimeException('Sale info: nameref is not found in passengers data');
            }
            $this->namref[$key] = $passenger['nameref'];
        }

        return $this;
    }

    /**
     * @param array $oldData
     * @param array $newData
     * @return array
     */
    public function compareSaleData(array $oldData, array $newData): array
    {
        $difference = [];
        foreach ($newData as $firstKey => $firstValue) {
            if (is_array($firstValue)) {
                if (!array_key_exists($firstKey, $oldData) || !is_array($oldData[$firstKey])) {
                    $difference[$firstKey] = '';
                } else {
                    $newDiff = $this->compareSaleData($oldData[$firstKey], $firstValue);
                    if (!empty($newDiff)) {
                        $difference[$firstKey] = $newDiff;
                    }
                }
            } elseif ((!array_key_exists($firstKey, $oldData) || $oldData[$firstKey] != $firstValue)) {
                if (!empty($firstValue) || !empty($oldData[$firstKey])) {
                    $difference[$firstKey] = $firstValue;
                }
            }
        }
        return $difference;
    }

    /**
     * @param CaseSale $caseSale
     * @param Cases $case
     * @param array $saleData
     * @return CaseSale
     */
    public function refreshOriginalSaleData(CaseSale $caseSale, Cases $case, array $saleData): CaseSale
    {
        if (isset($saleData['saleId']) && (int)$saleData['saleId'] === $caseSale->css_sale_id) {
            $caseSale = $this->casesSaleRepository->refreshOriginalSaleData($caseSale, $case, $saleData);
            $caseSale = $this->prepareAdditionalData($caseSale, $saleData);

            if (!$caseSale->save()) {
                \Yii::error([
                    'message' => 'An error occurred while trying to refresh original sale info;',
                    'caseGid' => $case->cs_gid,
                    'saleId' => $caseSale->css_sale_id,
                    'errors' => VarDumper::dumpAsString($caseSale->errors)
                ], 'CaseSaleService:refreshOriginalSaleData:CaseSale:save');
                throw new \RuntimeException('An error occurred while trying to refresh original sale info;');
            }

            $case->updateLastAction();
        } else {
            throw new \DomainException('Sale info form B/O is not equal with current info');
        }

        return $caseSale;
    }

    /**
     * @param CaseSale $caseSale
     * @param Cases $case
     * @param array $saleData
     * @param bool $createTicket
     * @return CaseSale
     * @throws \JsonException
     */
    public function saveAdditionalData(CaseSale $caseSale, Cases $case, array $saleData, bool $createTicket = true): ?CaseSale
    {
        if (isset($saleData['saleId'], $saleData['bookingId']) && (int) $saleData['saleId'] === (int) $caseSale->css_sale_id) {
            $caseSale->css_sale_data = $saleData;
            $caseSale->css_sale_data_updated = $caseSale->css_sale_data;

            $caseSale = $this->prepareAdditionalData($caseSale, $saleData);

            if (!$caseSale->save()) {
                throw new \RuntimeException(VarDumper::dumpAsString([
                    'message' => 'Additional data not saved',
                    'errors' => $caseSale->errors,
                    'saleData' => LogHelper::hidePersonalData($saleData, self::SENSITIVE_KEYS)
                ]), -1);
            }
            $case->updateLastAction();
            if ($createTicket) {
                $this->saleTicketService->createSaleTicketBySaleData($caseSale, $saleData);
            }
            return $caseSale;
        }
        throw new \RuntimeException('Error. Additional data not saved. Broken saleData params', -2);
    }

    /**
     * @param CaseSale $caseSale
     * @param array $saleData
     * @return CaseSale
     */
    public function prepareAdditionalData(CaseSale $caseSale, array $saleData): CaseSale
    {
        $caseSale->css_sale_pnr = $saleData['pnr'] ?? null;
        $caseSale->css_sale_created_dt = $saleData['created'] ?? null;
        //$caseSale->css_sale_book_id = $saleData['confirmationNumber'] ?? null;
        if (!empty($saleData['confirmationNumber'])) {
            $caseSale->css_sale_book_id = $saleData['confirmationNumber'];
        }
        $caseSale->css_sale_pax = $saleData['requestDetail']['passengersCnt'] ?? null;
        if (isset($saleData['price']['priceQuotes'])) {
            $amountCharged = 0;
            foreach ($saleData['price']['priceQuotes'] as $priceQuote) {
                if (isset($priceQuote['selling'])) {
                    $amountCharged += $priceQuote['selling'];
                }
            }
            $caseSale->css_charged = $amountCharged ?: null;
        }
        if (isset($saleData['price']['profit'])) {
            $caseSale->css_profit = $saleData['price']['profit'];
        }

        if (!isset($saleData['itinerary'])) {
            throw new \InvalidArgumentException('SaleData is broken. Index "itinerary" not found in sale data');
        }

        $this->prepareSegmentsData($caseSale, $saleData);

        if (isset($saleData['chargeType'])) {
            $caseSale->css_charge_type = $saleData['chargeType'];
        }
        return $caseSale;
    }

    public function prepareSegmentsData(CaseSale $caseSale, array $saleData): void
    {
        if (!isset($saleData['itinerary'])) {
            return;
        }

        $itineraryFirstKey = array_key_first($saleData['itinerary']);

        if (isset($saleData['itinerary'][$itineraryFirstKey]['segments'][0]['departureAirport'])) {
            $caseSale->css_out_departure_airport = $saleData['itinerary'][$itineraryFirstKey]['segments'][0]['departureAirport'];
        }
        if (isset($saleData['itinerary'][$itineraryFirstKey]['segments'])) {
            $idxLastInFirstSegments = count($saleData['itinerary'][$itineraryFirstKey]['segments']) - 1;
            if (isset($saleData['itinerary'][$itineraryFirstKey]['segments'][$idxLastInFirstSegments]['arrivalAirport'])) {
                $caseSale->css_out_arrival_airport = $saleData['itinerary'][$itineraryFirstKey]['segments'][$idxLastInFirstSegments]['arrivalAirport'];
            }
        }
        if (isset($saleData['itinerary'][$itineraryFirstKey]['segments'][0]['departureTime'])) {
            $caseSale->css_out_date = $saleData['itinerary'][$itineraryFirstKey]['segments'][0]['departureTime'];
        }

        if (count($saleData['itinerary']) > 1) {
            $itineraryLastKey = array_key_last($saleData['itinerary']);
            if (isset($saleData['itinerary'][$itineraryLastKey])) {
                if (isset($saleData['itinerary'][$itineraryLastKey]['segments'][0]['departureAirport'])) {
                    $caseSale->css_in_departure_airport = $saleData['itinerary'][$itineraryLastKey]['segments'][0]['departureAirport'];
                }
                $idxLastInLastSegments = count($saleData['itinerary'][$itineraryLastKey]['segments']) - 1;
                if (isset($saleData['itinerary'][$itineraryLastKey]['segments'][$idxLastInLastSegments]['arrivalAirport'])) {
                    $caseSale->css_in_arrival_airport = $saleData['itinerary'][$itineraryLastKey]['segments'][$idxLastInLastSegments]['arrivalAirport'];
                }
                if (isset($saleData['itinerary'][$itineraryLastKey]['segments'][0]['departureTime'])) {
                    $caseSale->css_in_date = $saleData['itinerary'][$itineraryLastKey]['segments'][0]['departureTime'];
                }
            }
        } else {
            $caseSale->css_in_departure_airport = null;
            $caseSale->css_in_arrival_airport = null;
            $caseSale->css_in_date = null;
        }
    }

    /**
     * @param array $params
     * @return array|mixed
     */
    public function searchRequestToBackOffice(array $params)
    {
        if (!Yii::$app->params['settings']['enable_request_to_bo_sale']) {
            return [];
        }
        try {
            $response = BackOffice::sendRequest2('cs/search', $params, 'POST', 120);

            if ($response->isOk) {
                $result = $response->data;
                if (isset($result['items']) && is_array($result['items']) && count($result['items'])) {
                    $lastSaleId = max(array_keys($result['items']));
                    return $result['items'][$lastSaleId];
                }
            } else {
                $responseStr = VarDumper::dumpAsString($response->content);
                throw new \RuntimeException('BO request Error: ' . $responseStr, -1);
            }
        } catch (\Throwable $throwable) {
            $message = VarDumper::dumpAsString([$throwable->getMessage(), $params], 20);
            if ($throwable->getCode() > 0) {
                Yii::error($message, 'CasesSaleService:searchRequestToBackOffice:Fail');
            } else {
                Yii::info($message, 'info\CasesSaleService:searchRequestToBackOffice:Fail');
            }
        }
        return [];
    }


    /**
     * @param int $sale_id
     * @param int $withFareRules
     * @param int $requestTime
     * @param int $withRefundRules
     * @return array
     */
    public function detailRequestToBackOffice(int $sale_id, int $withFareRules = 0, int $requestTime = 120, int $withRefundRules = 0): ?array
    {
        try {
            $data['sale_id'] = $sale_id;
            $data['withFareRules'] = $withFareRules;
            $data['withRefundRules'] = $withRefundRules;
            $response = BackOffice::sendRequest2('cs/detail', $data, 'POST', $requestTime);

            if ($response->isOk) {
                $result = $response->data;
                if (is_array($result) && array_key_exists('bookingId', $result)) {
                    return $result;
                }
                throw new BoResponseException('BO request Error. Broken data. : ' . VarDumper::dumpAsString($response));
            }
            throw new BoResponseException('BO request Error. Not isOk. : ' . VarDumper::dumpAsString($response->content));
        } catch (\Throwable $exception) {
            throw new BoResponseException($exception->getMessage());
        }
    }

    public function sendAddedCreditCardToBO(string $projectApiKey, string $bookingId, int $saleId, CreditCardForm $form, int $requestTime = 120): array
    {
        $response = [
            'error' => false,
            'message' => ''
        ];

        try {
            $data = [
                'apiKey' => $projectApiKey,
                'flightRequest' => [
                    'uid' => $bookingId,
                    'saleId' => $saleId
                ],
                'card' => [
                    'nickname' => $form->cc_holder_name,
                    'number' => (string)$form->cc_number,
                    'expiration_date' => $form->cc_expiration,
                    'cvv' => (string)$form->cc_cvv
                ]
            ];

            $host = Yii::$app->params['backOffice']['urlV3'];
            $responseBO = BackOffice::sendRequest2('payment/add-credit-card', $data, 'POST', $requestTime, $host);

            if ($responseBO->isOk) {
                $result = $responseBO->data;

                if (!(bool)$result['success']) {
                    $errors = '';
                    foreach ($result['errors'] as $error) {
                        if (is_array($error)) {
                            $errors .= implode('; ', $error);
                        } else {
                            $errors .= $error . '; ';
                        }
                    }
                    throw new \RuntimeException('BO add credit card request errors: ' . $errors);
                }
            } else {
                throw new \RuntimeException('BO add credit card request error. ' . VarDumper::dumpAsString($responseBO->content));
            }
        } catch (\Throwable $e) {
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @param string|null $order_uid
     * @param string|null $email
     * @param string|null $phone
     * @return array
     */
    public function getSaleFromBo(?string $order_uid = null, ?string $email = null, ?string $phone = null): array
    {
        $form = new CaseSaleRequestBoForm();
        $form->orderUid = $order_uid;
        $form->email = $email;
        $form->phone = $phone;

        if (!$form->validate()) {
            Yii::info(
                [$form->getErrors(), $form->getAttributes()],
                'info\CasesSaleService:getSaleFromBo:validate'
            );
            return [];
        }

        if ($order_uid && $result = $this->searchRequestToBackOffice(['confirmation_number' => $order_uid])) {
            return $result;
        }
        if ($email && $result = $this->searchRequestToBackOffice(['email' => $email])) {
            return $result;
        }
        if ($phone && $result = $this->searchRequestToBackOffice(['phone' => $phone])) {
            return $result;
        }
        return [];
    }

    /**
     * @param int $csId
     * @param array $saleData
     * @return CaseSale|null
     */
    public function createSale(int $csId, array $saleData): ?CaseSale
    {
        if ($this->isExistCaseSale($csId, $saleData['saleId'])) {
            return null;
        }

        try {
            if (!empty($saleData['saleId']) && $case = Cases::findOne($csId)) {
                $saleId = (int)$saleData['saleId'];

                if ($refreshSaleData = $this->detailRequestToBackOffice($saleId, 0, 120, 1)) {
                    $caseSale = new CaseSale();
                    $caseSale->css_cs_id = $csId;
                    $caseSale->css_sale_id = $saleId;
                    $caseSale->css_sale_book_id = $saleData['bookingId'] ?? $saleData['confirmationNumber'] ?? null;
                    $case->cs_order_uid = $caseSale->css_sale_book_id;
                    $this->casesRepository->save($case);

                    $caseSale = $this->saveAdditionalData($caseSale, $case, $refreshSaleData);

                    if (!$caseSale->save(false)) {
                        throw new \RuntimeException(VarDumper::dumpAsString([
                            'message' => 'CaseSale not saved from detailRequestToBackOffice',
                            'errors' => $caseSale->errors,
                            'saleData' => LogHelper::hidePersonalData($refreshSaleData, self::SENSITIVE_KEYS)
                        ]));
                    }

//                    $this->updateCaseProjectBySale($case, $refreshSaleData);

                    if ($caseSale->css_cs_id && SettingHelper::isEnableOrderFromSale()) {
                        $transaction = new Transaction(['db' => Yii::$app->db]);
                        try {
                            $bookingId = $refreshSaleData['baseBookingId'] ? $refreshSaleData['baseBookingId'] : $refreshSaleData['bookingId'];
                            if (!$order = OrderManageService::getBySaleIdOrBookingId($saleId, (string) $bookingId)) {
                                $orderCreateFromSaleForm = new OrderCreateFromSaleForm();
                                if (!$orderCreateFromSaleForm->load($refreshSaleData)) {
                                    throw new \RuntimeException('OrderCreateFromSaleForm not loaded');
                                }
                                if (!$orderCreateFromSaleForm->validate()) {
                                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($orderCreateFromSaleForm));
                                }
                                $order = $this->orderCreateFromSaleService->orderCreate($orderCreateFromSaleForm);
                                if (!$order->validate()) {
                                    throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($order));
                                }

                                $transaction->begin();
                                $orderId = $this->orderRepository->save($order);

                                $this->orderCreateFromSaleService->caseOrderRelation($orderId, $caseSale->css_cs_id);
                                $this->orderCreateFromSaleService->orderContactCreate($order, OrderContactForm::fillForm($refreshSaleData));

                                $currency = $orderCreateFromSaleForm->currency;
                                $this->flightFromSaleService->createHandler($order, $orderCreateFromSaleForm, $refreshSaleData);

                                if ($authList = ArrayHelper::getValue($refreshSaleData, 'authList')) {
                                    $this->orderCreateFromSaleService->paymentCreate($authList, $orderId, $currency);
                                }
                                $transaction->commit();
                            } else {
                                $this->orderCreateFromSaleService->caseOrderRelation($order->getId(), $caseSale->css_cs_id);
                            }
                        } catch (\Throwable $throwable) {
                            $transaction->rollBack();
                            $message = AppHelper::throwableLog($throwable, true);
                            $message['saleData'] = LogHelper::hidePersonalData($refreshSaleData, self::SENSITIVE_KEYS);
                            Yii::error($message, 'CasesSaleService:createOrderStructureFromSale:Throwable');
                        }
                    }
                } else {
                    throw new \RuntimeException('Error. Broken response from detailRequestToBackOffice. CaseSale not updated.');
                }
                return $caseSale;
            }
            throw new \RuntimeException('Error. Param saleId is empty or Case not found');
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'CasesSaleService:createSale:Throwable');
        }
        return null;
    }

    public function createSaleByData(int $caseId, array $saleData): ?CaseSale
    {
        if ($this->isExistCaseSale($caseId, $saleData['saleId'])) {
            throw new \RuntimeException('CaseSale already exist. Case(' . $caseId . ') Sale(' . $saleData['saleId'] . ')');
        }

        $cs = new CaseSale();
        $cs->css_cs_id = $caseId;
        $cs->css_sale_id = $saleData['saleId'];
        $cs->css_sale_data = $saleData;
        $cs->css_sale_pnr = $saleData['pnr'] ?? null;
        $cs->css_sale_created_dt = $saleData['created'] ?? null;
        $cs->css_sale_book_id = $saleData['bookingId'] ?? null;
        $cs->css_sale_pax = isset($saleData['passengers']) && is_array($saleData['passengers']) ? count($saleData['passengers']) : null;
        $cs->css_sale_data_updated = $cs->css_sale_data;

        $cs = $this->prepareAdditionalData($cs, $saleData);

        return $this->casesSaleRepository->save($cs);
    }

    /**
     * @param int $csId
     * @param int $saleId
     * @return bool
     */
    public function isExistCaseSale(int $csId, int $saleId): bool
    {
        return CaseSale::find()->where(['css_cs_id' => $csId, 'css_sale_id' => $saleId])->exists();
    }

    public function sendCcInfo(string $apiKey, int $saleId, string $bookId, string $email): array
    {
        $result = [
            'error' => false,
            'message' => ''
        ];

        $data = [
            'apiKey' => $apiKey,
            'flightRequest' => [
                'uid' => $bookId,
                'saleId' => $saleId
            ],
            'email' => $email
        ];
        $response = BackOffice::sendRequest2('payment/invoke-new-cc-info', $data, 'POST', 30, Yii::$app->params['backOffice']['urlV3'], true);
        if ($response->isOk) {
            $responseData = $response->data;
            Yii::info(VarDumper::dumpAsString($responseData), 'info\CasesSaleService::sendCcInfo::BOResponse');
            if (!$responseData['success']) {
                $result['error'] = true;
                $result['message'] = reset($responseData['errors'])[0] ?? 'Unknown error message from B/O';
            }
            return $result;
        }

        $result['error'] = true;
        $result['message'] = Json::decode($response->content)['message'] ?? 'Unknown error message from B/O';

        return $result;
    }

    public function getSaleData(string $bookingId): array
    {
        $saleSearch = $this->getSaleFromBo($bookingId);
        if (empty($saleSearch['saleId'])) {
            throw new BoResponseException('Sale not found by Booking ID(' . $bookingId . ') from "cs/search"');
        }
        return $this->detailRequestToBackOffice($saleSearch['saleId'], 0, 120, 1);
    }

    /**
     * @param Cases $case
     * @param array $refreshSaleData
     */
    public function updateCaseProjectBySale(Cases $case, array $refreshSaleData)
    {
        try {
            $saleProjectApiKey = $refreshSaleData['projectApiKey'] ?? null;

            if (!empty($saleProjectApiKey)) {
                $allCaseSales = $case->getCaseSale()->all();
                $allCaseSalesProjectApi = [];

                foreach ($allCaseSales as $caseSale) {
                    $existSaleData = isset($caseSale->css_sale_data['projectApiKey']);
                    if ($existSaleData && !in_array($caseSale->css_sale_data['projectApiKey'], $allCaseSalesProjectApi)) {
                        $allCaseSalesProjectApi[] = $caseSale->css_sale_data['projectApiKey'];
                    }
                }

                if (count($allCaseSalesProjectApi) == 1 && isset($allCaseSalesProjectApi[0])) {
                    $caseProject = $case->project;
                    if ($caseProject) {
                        if ($caseProject->api_key !== trim($saleProjectApiKey)) {
                            $newProject = Project::findOne(['api_key' => $saleProjectApiKey]);
                            if ($newProject) {
                                $case->cs_project_id = $newProject->id;
                                $this->casesRepository->save($case);

                                $description = 'Case project was changed.';
                                $data = [
                                    'old_project' => $caseProject->name,
                                    'new_project' => $newProject->name,
                                ];

                                $case->addEventLog(CaseEventLog::CASE_UPDATE_INFO, $description, $data);
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'CasesSaleService:updateCaseProject:Throwable');
        }
    }
}

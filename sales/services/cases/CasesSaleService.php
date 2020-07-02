<?php

namespace sales\services\cases;

use common\components\BackOffice;
use common\models\CaseSale;
use Exception;
use frontend\models\form\CreditCardForm;
use http\Exception\RuntimeException;
use sales\entities\cases\Cases;
use sales\helpers\app\AppHelper;
use sales\model\saleTicket\useCase\create\SaleTicketService;
use sales\repositories\cases\CasesSaleRepository;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class CasesSaleService
{
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
	 * @var SaleTicketService
	 */
	private $saleTicketService;

	/**
	 * CasesSaleService constructor.
	 * @param CasesSaleRepository $casesSaleRepository
	 * @param SaleTicketService $saleTicketService
	 */
	public function __construct(CasesSaleRepository $casesSaleRepository, SaleTicketService $saleTicketService)
	{
		$this->casesSaleRepository = $casesSaleRepository;
		$this->saleTicketService = $saleTicketService;
	}

	/**
	 * @param CaseSale $caseSale
	 * @return array
	 * @throws Exception
	 */
	public function prepareSaleData(CaseSale $caseSale): array
	{
		$originalData = json_decode( (string)$caseSale->css_sale_data, true );
		$updatedData = json_decode( (string)$caseSale->css_sale_data_updated, true );

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
		$updatedData = json_decode((string)$caseSale->css_sale_data_updated, true);

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
		$updatedData = json_decode((string)$caseSale->css_sale_data_updated, true);

		$this->validatingCarrier = $updatedData['validatingCarrier'];

		return $this;
	}

	/**
	 * @param CaseSale $caseSale
	 * @return bool
	 */
	public function isDataBackedUpToOriginal(CaseSale $caseSale): bool
	{
		$oldData = json_decode((string)$caseSale->css_sale_data, true);
		$newData = json_decode((string)$caseSale->css_sale_data_updated, true);
		$difference = $this->compareSaleData($oldData, $newData);

		return !$difference ? true : false;
	}

	/**
	 * @param array $passengers
	 * @return bool
	 */
	public function checkIfPassengersHasNamerefAttribute(array  $passengers): bool
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
			$passenger[$key][$segmentKey+1] = $value;
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

			if(!$caseSale->save()) {
				\Yii::error(VarDumper::dumpAsString(['errors' => $caseSale->errors, 'saleData' => $saleData]), 'CasesController:actionAddSale:CaseSale:save');
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
     * @return CaseSale
     */
    public function saveAdditionalData(CaseSale $caseSale, Cases $case, array $saleData): ?CaseSale
    {
        if ((isset($saleData['saleId']) && (int)$saleData['saleId'] === (int)$caseSale->css_sale_id) && isset($saleData['bookingId'])) {
            $caseSale->css_sale_data = json_encode($saleData, JSON_THROW_ON_ERROR);
            $caseSale->css_sale_data_updated = $caseSale->css_sale_data;

            $caseSale = $this->prepareAdditionalData($caseSale, $saleData);

            if(!$caseSale->save()) {
                \Yii::error(VarDumper::dumpAsString(['errors' => $caseSale->errors, 'saleData' => $saleData]), 'CasesSaleService:saveAdditionalData');
                throw new \RuntimeException('Error. Additional data not saved');
            }
            $case->updateLastAction();
			$this->saleTicketService->createSaleTicketBySaleData($caseSale, $saleData);
            return $caseSale;
        }
        throw new \RuntimeException('Error. Additional data not saved. Broken saleData params');
    }

	/**
     * @param CaseSale $caseSale
     * @param array $saleData
     * @return CaseSale
     */
    public function prepareAdditionalData(CaseSale $caseSale, array $saleData): CaseSale
    {
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
        if (isset($saleData['itinerary'][0]['segments'][0]['departureAirport'])) {
            $caseSale->css_out_departure_airport = $saleData['itinerary'][0]['segments'][0]['departureAirport'];
        }
        if (isset($saleData['itinerary'][0]['segments'])) {
            $idxLastInFirstSegments = count($saleData['itinerary'][0]['segments']) - 1;
            if (isset($saleData['itinerary'][0]['segments'][$idxLastInFirstSegments]['arrivalAirport'])) {
                $caseSale->css_out_arrival_airport = $saleData['itinerary'][0]['segments'][$idxLastInFirstSegments]['arrivalAirport'];
            }
        }
        if (isset($saleData['itinerary'][0]['segments'][0]['departureTime'])) {
            $caseSale->css_out_date = $saleData['itinerary'][0]['segments'][0]['departureTime'];
        }
        if (isset($saleData['itinerary'])) {
            $countItinerary = count($saleData['itinerary']);
            if (isset($saleData['itinerary'][$countItinerary - 1]['segments'][0]['departureAirport'])) {
                $caseSale->css_in_departure_airport = $saleData['itinerary'][$countItinerary - 1]['segments'][0]['departureAirport'];
            }
            $idxLastInLastSegments = count($saleData['itinerary'][$countItinerary - 1]['segments']) - 1;
            if (isset($saleData['itinerary'][$countItinerary - 1]['segments'][$idxLastInLastSegments]['arrivalAirport'])) {
                $caseSale->css_out_arrival_airport = $saleData['itinerary'][$countItinerary - 1]['segments'][$idxLastInLastSegments]['arrivalAirport'];
            }
            if (isset($saleData['itinerary'][$countItinerary - 1]['segments'][0]['departureTime'])) {
                $caseSale->css_in_date = $saleData['itinerary'][$countItinerary - 1]['segments'][0]['departureTime'];
            }
        }
        if (isset($saleData['chargeType'])) {
            $caseSale->css_charge_type = $saleData['chargeType'];
        }
        return $caseSale;
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
                throw new \RuntimeException('BO request Error: ' . $responseStr, 20);
            }
        } catch (\Throwable $exception) {
            \Yii::error(VarDumper::dumpAsString($exception, 20),'CasesSaleService:searchRequestToBackOffice:Fail');
        }
        return [];
    }


    /**
     * @param int $sale_id
     * @param int $withFareRules
     * @param int $requestTime
     * @param int $withRefundRules
     * @return array
     * @throws BadRequestHttpException
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
                throw new \RuntimeException('BO request Error. Broken data. : ' . VarDumper::dumpAsString($response));
            } else {
                throw new \RuntimeException('BO request Error. Not isOk. : ' . VarDumper::dumpAsString($response->content));
            }
        } catch (\Throwable $exception) {
            throw new BadRequestHttpException($exception->getMessage());
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

			$host = Yii::$app->params['backOffice']['serverUrlV3'];
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

        $transaction = Yii::$app->db->beginTransaction();
        try {
			if (!empty($saleData['saleId']) && $case = Cases::findOne($csId)) {
                $saleId = (int)$saleData['saleId'];

                $caseSale = new CaseSale();
                $caseSale->css_cs_id = $csId;
                $caseSale->css_sale_id = $saleId;
                $caseSale->css_sale_data = json_encode($saleData, JSON_THROW_ON_ERROR);

                if (!$caseSale->save()) {
                    throw new \RuntimeException('Error. CaseSale not saved.');
                }

                if ($refreshSaleData = $this->detailRequestToBackOffice($saleId, 0, 120, 1)) {
                    $caseSale->css_sale_pnr = $saleData['pnr'] ?? null;
                    $caseSale->css_sale_created_dt = $saleData['created'] ?? null;
                    $caseSale->css_sale_book_id = $saleData['confirmationNumber'] ?? null;
                    $caseSale->css_sale_pax = $saleData['requestDetail']['passengersCnt'] ?? null;
                    $caseSale->css_sale_data = json_encode($refreshSaleData, JSON_THROW_ON_ERROR);

                    $caseSale = $this->saveAdditionalData($caseSale, $case, $refreshSaleData);

                    if (!$caseSale->save(false)) {
                        \Yii::error(VarDumper::dumpAsString(['errors' => $caseSale->errors, 'saleData' => $saleData]),
                        'CasesSaleService:createSale:Update');
                        throw new \RuntimeException('Error. CaseSale not updated from detailRequestToBackOffice.');
                    }
                } else {
                    throw new \RuntimeException('Error. Broken response from detailRequestToBackOffice. CaseSale not updated.');
                }
                $transaction->commit();

                return $caseSale;
            }

            throw new \RuntimeException('Error. Params csId and saleId is required');
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            Yii::error(AppHelper::throwableFormatter($throwable), 'CasesSaleService:createSale:Throwable' );
        }

        return null;
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
}
<?php

namespace sales\services\cases;

use common\components\BackOffice;
use common\models\CaseSale;
use Exception;
use http\Exception\RuntimeException;
use sales\entities\cases\Cases;
use sales\repositories\cases\CasesSaleRepository;
use yii\helpers\VarDumper;

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
	 * CasesSaleService constructor.
	 * @param CasesSaleRepository $casesSaleRepository
	 */
	public function __construct(CasesSaleRepository $casesSaleRepository)
	{
		$this->casesSaleRepository = $casesSaleRepository;
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
				\Yii::error(VarDumper::dumpAsString($caseSale->errors). ' Data: ' . VarDumper::dumpAsString($saleData), 'CasesController:actionAddSale:CaseSale:save');
				throw new \RuntimeException('An error occurred while trying to refresh original sale info;');
			}

			$case->updateLastAction();
		} else {
			throw new \DomainException('Sale info form B/O is not equal with current info');
		}

		return $caseSale;
	}

    /**
     * @param Cases $case
     * @param array $saleData
     * @return CaseSale
     */
    public function create(Cases $case, array $saleData): CaseSale
	{
		$caseSale = new CaseSale();
        $caseSale->css_cs_id = $case->cs_id;
        $caseSale->css_sale_id = $saleData['saleId'];
        $caseSale->css_sale_data = json_encode($saleData);
        $caseSale->css_sale_pnr = $saleData['pnr'] ?? null;
        $caseSale->css_sale_created_dt = $saleData['created'] ?? null;
        $caseSale->css_sale_book_id = $saleData['bookingId'] ?? null;
        $caseSale->css_sale_pax = isset($saleData['passengers']) && is_array($saleData['passengers']) ? count($saleData['passengers']) : null;
        $caseSale->css_sale_data_updated = $caseSale->css_sale_data;

        $caseSale = $this->prepareAdditionalData($caseSale, $saleData);

        if(!$caseSale->save()) {
            \Yii::error(VarDumper::dumpAsString($caseSale->errors). ' Data: ' . VarDumper::dumpAsString($saleData), 'CasesSaleService:create');
        }

        $case->updateLastAction();

		return $caseSale;
	}

	/**
     * @param CaseSale $caseSale
     * @param array $saleData
     * @return CaseSale
     */
    public function prepareAdditionalData(CaseSale $caseSale, array $saleData): CaseSale
    {
        if (isset($saleData['price']['amountCharged'])) {
            $amountCharged = preg_replace('/[^0-9.]/', '', $saleData['price']['amountCharged']);
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
    public function requestToBackOffice(array $params)
    {
        try {
            $response = BackOffice::sendRequest2('cs/detail', $params);
            if ($response->isOk) {
                $result = $response->data;
                if ($result && is_array($result)) {
                    return $result;
                }
            } else {
                throw new Exception('BO request Error: ' . VarDumper::dumpAsString($response->content), 10);
            }
        } catch (\Throwable $exception) {
            \Yii::error(VarDumper::dumpAsString($exception), 'CasesSaleService:requestBackOffice');
        }
        return [];
    }

}
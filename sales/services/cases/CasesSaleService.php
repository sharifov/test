<?php

namespace sales\services\cases;

use common\models\CaseSale;
use sales\repositories\cases\CasesSaleRepository;

class CasesSaleService
{
	private const FORMAT_PASSENGERS_ATTRIBUTES = [
		'meal' => ''
	];

	/**
	 * @var CasesSaleRepository
	 */
	private $casesSaleRepository;

	/**
	 * CasesSaleService constructor.
	 * @param CasesSaleRepository $casesSaleRepository
	 */
	public function __construct(CasesSaleRepository $casesSaleRepository)
	{
		$this->casesSaleRepository = $casesSaleRepository;
	}

	/**
	 * @param int $caseId
	 * @param int $caseSaleId
	 * @return array
	 */
	public function getSaleData(int $caseId, int $caseSaleId): array
	{
		$caseSale = $this->casesSaleRepository->getSaleByPrimaryKeys($caseId, $caseSaleId);

		$originalData = $this->casesSaleRepository->decodeSaleData((string)$caseSale->css_sale_data);
		$updatedData = $this->casesSaleRepository->decodeSaleData((string)$caseSale->css_sale_data_updated);

		$difference = $this->compareSaleData($originalData, $updatedData);

		$this->preparePassengersData($difference, $updatedData);

		print_r($difference);die;

		return $difference;
	}

	/**
	 * @param array $saleData
	 * @param array $updatedData
	 *
	 * @example {array} Request-Example:
	 *
	 * 	[
	 * 		[1.1] => [
	 * 			["birth_date"] => "1969-11-26",
	 * 			["gender"] => "M",
	 * 			["meal"] => [
	 * 					[1] => "VGML",
	 * 					[2] => "VGML",
	 * 					[3] => "VGML"
	 * 				]
	 * 		],
	 * 		[2.1] =>  [
	 * 			["birth_date"] => "1994-03-27"
	 * 		]
	 * 	]
	 *
	 */
	private function preparePassengersData(array &$saleData, array $updatedData): void
	{
		if ($saleData['passengers']) {
			foreach ($saleData['passengers'] as $key => $passenger) {
				if ($passenger['meal']) {
					$this->formatPassengersMeal($saleData['passengers'][$key], $updatedData['itinerary']);
				}

				unset($saleData['passengers'][$key]);
				$saleData['passengers'][++$key . '.1'] = $passenger;
			}
		}
	}

	/**
	 * @param array $passengerData
	 * @param array $itinerary
	 */
	private function formatPassengersMeal(array &$passengerData, array $itinerary): void
	{
		$meal = $passengerData['meal'];
		if (!is_array($passengerData['meal'])) $passengerData['meal'] = [];
		$passengerData['meal'] = 'asdads';
//		foreach ($itinerary as $key => $item) {
//			if ($item['segments']) {
//				for($i = 1; $i <= count($item['segments']); $i++) {
//					$passengerData['meal'][$i] = $meal;
//				}
//			}
//		}
	}

	/**
	 * @param array $oldData
	 * @param array $newData
	 * @return array
	 */
	private function compareSaleData(array $oldData, array $newData): array
	{
		$difference = [];
		foreach ($newData as $firstKey => $firstValue) {
			if (is_array($firstValue)) {
				if (!array_key_exists($firstKey, $oldData) || !is_array($oldData[$firstKey])) {
					$difference[$firstKey] = '';
				} else {
					$newDiff = $this->compareSaleData($firstValue, $oldData[$firstKey]);
					if (!empty($newDiff)) {
						$difference[$firstKey] = $newDiff;
					}
				}
			} else {
				if ((!array_key_exists($firstKey, $oldData) || $oldData[$firstKey] != $firstValue) && !empty($newData[$firstKey])) {
					$difference[$firstKey] = $newData[$firstKey];
				}
			}
		}
		return $difference;
	}
}
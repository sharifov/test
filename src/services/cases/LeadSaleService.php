<?php

namespace src\services\cases;

use src\model\sale\SaleDetail;
use Yii;

class LeadSaleService
{
    private const CACHE_KEY = 'sale-%d';

    private CasesSaleService $casesSaleService;

    public function __construct(
        CasesSaleService $casesSaleService
    ) {
        $this->casesSaleService = $casesSaleService;
    }

    /**
     * @param int $leadId
     * @return false|mixed|SaleDetail
     */
    public function getSaleByBoFlightId(int $bo_flight_id)
    {
        $keyCache = sprintf(self::CACHE_KEY, $bo_flight_id);

        $sale = \Yii::$app->cacheFile->get($keyCache);

        if ($sale === false) {
            $data = $this->casesSaleService->detailRequestToBackOffice($bo_flight_id);
            $sale = $this->prepareSaleDetail($data);
            Yii::$app->cacheFile->set($keyCache, $sale, 60);
        }

        return $sale;
    }

    private function prepareSaleDetail(array $data): SaleDetail
    {
        $saleDetail = new SaleDetail();
        $saleDetail->load($data, '');
        return $saleDetail;
    }
}

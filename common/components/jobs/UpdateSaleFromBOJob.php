<?php

namespace common\components\jobs;

use common\models\CaseSale;
use sales\services\cases\CasesSaleService;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 *
 * @property float|int $ttr
 */
class UpdateSaleFromBOJob extends BaseObject implements JobInterface
{
    public int $caseId;
    public int $saleId;
    public int $requestTime = 120;
    public int $withFareRules = 0;
    public int $withRefundRules = 1;
    public int $cacheDuration = 120;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue) : bool
    {
        try {
            if($this->checkParams()) {
                /** @var CasesSaleService $casesSaleService */
                $casesSaleService = Yii::createObject(CasesSaleService::class);

                $cacheKeySale = 'detailRequestToBackOffice_' . $this->saleId;
                $refreshSaleData = Yii::$app->cache->get($cacheKeySale);

                if ($refreshSaleData === false) {
                    if ($refreshSaleData = $casesSaleService->detailRequestToBackOffice(
                        $this->saleId,
                        $this->withFareRules,
                        $this->requestTime,
                        $this->withRefundRules)
                    ) {
                        Yii::$app->cache->set($cacheKeySale, $refreshSaleData, $this->cacheDuration);
                    }
                    throw new \RuntimeException('Error. Response from detailRequestToBackOffice is empty');
                }
                if ($caseSale = CaseSale::findOne(['css_cs_id' => $this->caseId, 'css_sale_id' => $this->saleId])) {
                    $caseSale->css_sale_data = json_encode($refreshSaleData, JSON_THROW_ON_ERROR); /* TODO:: not convert after SL-1864 */
                    if (!$caseSale->save(false)) {
                        throw new \RuntimeException('Error. CaseSale not updated from detailRequestToBackOffice.');
                    }
                } else {
                    throw new \RuntimeException('Error. CaseSale (' . $this->caseId . '/' . $this->saleId . ') not found.');
                }
            } else {
                throw new \RuntimeException('Error. Params "caseId" and "saleId" is required');
            }
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'UpdateSaleFromBOJob:execute:Throwable');
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function checkParams(): bool
    {
        return ($this->caseId && $this->saleId);
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 20;
    }
}
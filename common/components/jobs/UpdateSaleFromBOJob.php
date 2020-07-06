<?php

namespace common\components\jobs;

use common\models\CaseSale;
use sales\entities\cases\Cases;
use sales\model\saleTicket\useCase\create\SaleTicketService;
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
                /** @var SaleTicketService $saleTicketService */
                $saleTicketService = Yii::createObject(SaleTicketService::class);

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
                    }  else {
                        throw new \RuntimeException('Response from detailRequestToBackOffice is empty. SaleId (' . $this->saleId . ')', 101);
                    }
                }
                $case = Cases::findOne($this->caseId);
                if ($case && $caseSale = CaseSale::findOne(['css_cs_id' => $this->caseId, 'css_sale_id' => $this->saleId])) {
                    $casesSaleService->saveAdditionalData($caseSale, $case, $refreshSaleData, true);
                } else {
                    throw new \RuntimeException('CaseSale (' . $this->caseId . '/' . $this->saleId . ') not found.', 102);
                }
            } else {
                throw new \RuntimeException('Params "caseId" and "saleId" is required', 103);
            }
        } catch (\Throwable $throwable) {
            if ($throwable->getCode() > 100) {
                Yii::info(VarDumper::dumpAsString($throwable->getMessage()),
                'info\UpdateSaleFromBOJob:execute:Throwable');
            } else {
                Yii::error(VarDumper::dumpAsString($throwable->getMessage()),
                'UpdateSaleFromBOJob:execute:Throwable');
            }
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
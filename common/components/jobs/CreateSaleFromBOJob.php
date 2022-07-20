<?php

namespace common\components\jobs;

use src\services\cases\CasesSaleService;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 *
 * @property float|int $ttr
 * @property int $case_id
 * @property string|null $order_uid
 * @property string|null $email
 * @property string|null $phone
 * @property CasesSaleService $casesSaleService
 */
class CreateSaleFromBOJob extends BaseJob implements JobInterface
{
    public $case_id;
    public $order_uid;
    public $email;
    public $phone;
    public $project_key;

    private $casesSaleService;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->waitingTimeRegister();
        try {
            if ($this->checkParams()) {
                $this->casesSaleService = Yii::createObject(CasesSaleService::class);

                $saleData = $this->casesSaleService->getSaleFromBo($this->project_key, $this->order_uid, $this->email, $this->phone);
                if (count($saleData) && isset($saleData['saleId'])) {
                    $keyCasesSale = $this->case_id . '-' . $saleData['saleId'];
                    $existCasesSale = Yii::$app->cache->get($keyCasesSale);

                    if ($existCasesSale === false) {
                        Yii::$app->cache->set($keyCasesSale, $keyCasesSale, 60);
                        $this->casesSaleService->createSale($this->case_id, $saleData);
                    }
                }
            } else {
                throw new \RuntimeException('Error. Params csId and (order_uid||email||phone) is required');
            }
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'CreateSaleFromBOJob:execute:catch');
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function checkParams(): bool
    {
        return ($this->case_id && ($this->order_uid || $this->email || $this->phone));
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 20;
    }
}

<?php

/**
 * Created by Alex Connor.
 * User: alexandr
 * Date: 2019-04-22
 */

namespace common\components\jobs;

use common\components\SearchService;
use common\models\Lead;
use src\dto\searchService\SearchServiceQuoteDTO;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 * This is the model class for table "Lead".
 *
 * @property int $lead_id
 */

class QuickSearchInitPriceJob extends BaseJob implements JobInterface
{
    public $lead_id;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->waitingTimeRegister();
        try {
            if ($this->lead_id) {
                $lead = Lead::findOne($this->lead_id);
                if ($lead) {
                    $dto = new SearchServiceQuoteDTO($lead);
                    $result = SearchService::getOnlineQuotes($dto);
                    if ($result && isset($result['data']['results'][0]['prices']['totalPrice'])) {
                        $minPrice = (double) $result['data']['results'][0]['prices']['totalPrice'];
                        $lead->l_init_price = $minPrice;
                        $lead->update();
                    }
                }
            }
        } catch (\Throwable $e) {
            Yii::error(VarDumper::dumpAsString($e->getMessage()), 'QuickSearchInitPriceJob:execute:catch');
        }
        return false;
    }

    public function getTtr()
    {
        return 1 * 5;
    }

    /*public function canRetry($attempt, $error)
    {
        return ($attempt < 5) && ($error instanceof TemporaryException);
    }*/
}

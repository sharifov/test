<?php

namespace common\components\jobs;

use common\components\ga\GaQuote;
use common\models\Quote;
use sales\helpers\app\AppHelper;
use yii\base\BaseObject;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;
use Yii;
use yii\queue\Queue;

/**
 *
 * @property float|int $ttr
 */
class SendQuoteInfoToGaJob extends BaseJob implements JobInterface
{
    public Quote $quote;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->executionTimeRegister();
        try {
            if ($this->checkParams() && $gaQuote = new GaQuote($this->quote)) {
                $response = $gaQuote->send();
                if ($response) {
                    Yii::info(
                        [
                            'quoteId' => $this->quote->id,
                            'message' => 'Info sent to GA',
                            'data' => $gaQuote->getPostData()
                        ],
                        'info\SendQuoteInfoToGaJob:execute:sent'
                    );
                } else {
                    Yii::warning(
                        [
                            'quoteId' => $this->quote->id,
                            'message' => 'Info NOT sent to GA',
                            'data' => $gaQuote->getPostData()
                        ],
                        'SendQuoteInfoToGaJob:execute'
                    );
                }
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'SendQuoteInfoToGaJob:execute:Throwable');
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function checkParams(): bool
    {
        return $this->quote->lead->isReadyForGa();
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 20;
    }
}

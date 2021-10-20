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
    public int $quoteId;

    /**
     * @param Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->waitingTimeRegister();
        try {
            if (!$quote = Quote::findOne(['id' => $this->quoteId])) {
                throw new \DomainException('Quote not found');
            }

            if ($this->checkParams($quote) && $gaQuote = new GaQuote($quote)) {
                $response = $gaQuote->send();
                if (!$response) {
                    throw new \DomainException('response is empty');
                }

                if ($response->isOk) {
                    Yii::info(
                        [
                            'quoteId' => $this->quoteId,
                            'message' => 'Info sent to GA',
                            'responseContent' => VarDumper::dumpAsString($response->content),
                            'data' => $gaQuote->getPostData()
                        ],
                        'info\SendQuoteInfoToGaJob:execute:sent'
                    );
                } else {
                    Yii::warning(
                        [
                            'quoteId' => $this->quoteId,
                            'message' => 'Info NOT sent to GA',
                            'responseContent' => VarDumper::dumpAsString($response->content),
                            'data' => $gaQuote->getPostData()
                        ],
                        'SendQuoteInfoToGaJob:execute:warning'
                    );
                }
            }
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable, true);
            $message['quoteId'] = $this->quoteId ?? null;
            \Yii::error(
                $message,
                'SendQuoteInfoToGaJob:execute:Throwable'
            );
        }
        return false;
    }

    /**
     * @param Quote $quote
     * @return bool
     */
    protected function checkParams(Quote $quote): bool
    {
        return $quote->lead->isReadyForGa();
    }

    /**
     * @return float|int
     */
    public function getTtr()
    {
        return 1 * 20;
    }
}

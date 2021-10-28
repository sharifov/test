<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\components\jobs\BaseJob;
use frontend\helpers\JsonHelper;
use modules\flight\models\FlightRequest;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class VoluntaryRefundCreateJob
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property-read int $flightRequestId
 * @property-read int|null $productQuoteId
 */
class VoluntaryRefundCreateJob extends BaseJob implements JobInterface
{
    private int $flightRequestId;
    private ?int $productQuoteId;

    public function __construct(int $flightRequestId, ?int $productQuoteId, ?float $timeStart = null, $config = [])
    {
        parent::__construct($timeStart, $config);
        $this->flightRequestId = $flightRequestId;
        $this->productQuoteId = $productQuoteId;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();

        $voluntaryRefundService = \Yii::createObject(VoluntaryRefundService::class);

        if (!$flightRequest = FlightRequest::findOne($this->flightRequestId)) {
            throw new \DomainException('FlightRequest not found, ID (' . $this->flightRequestId . ')');
        }

        try {
            $voluntaryRefundCreateForm = new VoluntaryRefundCreateForm();
            if (!$voluntaryRefundCreateForm->load(JsonHelper::decode($flightRequest->fr_data_json, true)) || !$voluntaryRefundCreateForm->validate()) {
                throw new \RuntimeException('Invalid FlightRequest data json');
            }

            if ($this->productQuoteId && $productQuote = ProductQuote::findOne(['pq_id' => $this->productQuoteId])) {
                $voluntaryRefundService->processProductQuote($productQuote)->startRefundAutoProcess($voluntaryRefundCreateForm, $flightRequest->project, $productQuote);
            } else {
                $voluntaryRefundService->startRefundAutoProcess($voluntaryRefundCreateForm, $flightRequest->project, null);
            }

            $flightRequest->statusToDone();
            $flightRequest->save();
        } catch (VoluntaryRefundCodeException $e) {
            $flightRequest->statusToError();
            $flightRequest->save();
        } catch (\Throwable $e) {
            $flightRequest->statusToError();
            $flightRequest->save();
            \Yii::error(AppHelper::throwableLog($e, true), 'VoluntaryRefundCreateJob:RuntimeException:DomainException:');
        }
    }
}

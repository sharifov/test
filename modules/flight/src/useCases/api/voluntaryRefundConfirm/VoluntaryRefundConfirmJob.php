<?php

namespace modules\flight\src\useCases\api\voluntaryRefundConfirm;

use common\components\jobs\BaseJob;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundCodeException;
use sales\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class VoluntaryRefundConfirmJob
 * @package modules\flight\src\useCases\api\voluntaryRefundConfirm
 *
 * @property-read int $flightRequestId
 * @property-read int $productQuoteRefundId
 * @property-read bool $boRequestConfirmResult
 */
class VoluntaryRefundConfirmJob extends BaseJob implements JobInterface
{
    private int $flightRequestId;
    private int $productQuoteRefundId;
    private bool $boRequestConfirmResult;

    public function __construct(
        int $flightRequestId,
        int $productQuoteRefundId,
        bool $boRequestConfirmResult,
        ?float $timeStart = null,
        $config = []
    ) {
        parent::__construct($timeStart, $config);
        $this->flightRequestId = $flightRequestId;
        $this->productQuoteRefundId = $productQuoteRefundId;
        $this->boRequestConfirmResult = $boRequestConfirmResult;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();

        if (!$flightRequest = FlightRequest::findOne($this->flightRequestId)) {
            throw new \DomainException('FlightRequest not found, ID (' . $this->flightRequestId . ')');
        }

        try {
//            $voluntaryRefundCreateForm = new VoluntaryRefundCreateForm();
//            if (!$voluntaryRefundCreateForm->load(JsonHelper::decode($flightRequest->fr_data_json, true)) || !$voluntaryRefundCreateForm->validate()) {
//                throw new \RuntimeException('Invalid FlightRequest data json');
//            }
//
//            if ($this->productQuoteId && $productQuote = ProductQuote::findOne(['pq_id' => $this->productQuoteId])) {
//                $voluntaryRefundService->processProductQuote($productQuote, $voluntaryRefundCreateForm, $flightRequest->fr_project_id);
//            } else {
//                $voluntaryRefundService->startRefundAutoProcess($voluntaryRefundCreateForm, $flightRequest->fr_project_id, null);
//            }
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

<?php

declare(strict_types=1);

namespace modules\flight\src\useCases\api\voluntaryRefundExpired;

use common\components\jobs\BaseJob;
use DomainException;
use modules\flight\models\FlightRequest;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefundRepository;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use src\helpers\app\AppHelper;
use Throwable;
use Yii;
use yii\queue\JobInterface;

class VoluntaryRefundExpiredJob extends BaseJob implements JobInterface
{
    private int $flightRequestId;
    private int $productQuoteRefundId;

    public function __construct(
        int $flightRequestId,
        int $productQuoteRefundId,
        ?float $timeStart = null,
        $config = []
    ) {
        parent::__construct($timeStart, $config);
        $this->flightRequestId = $flightRequestId;
        $this->productQuoteRefundId = $productQuoteRefundId;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();

        try {
            if (!$flightRequest = FlightRequest::findOne($this->flightRequestId)) {
                throw new DomainException('FlightRequest not found, ID (' . $this->flightRequestId . ')');
            }

            $productQuoteRefundRepository = Yii::createObject(ProductQuoteRefundRepository::class);
            $productQuoteObjectRefundRepository = Yii::createObject(ProductQuoteObjectRefundRepository::class);
            $productQuoteOptionRefundRepository = Yii::createObject(ProductQuoteOptionRefundRepository::class);

            $productQuoteRefund = $productQuoteRefundRepository->find($this->productQuoteRefundId);

            $productQuoteRefund->expired();
            $productQuoteRefundRepository->save($productQuoteRefund);

            foreach ($productQuoteRefund->productQuoteObjectRefunds ?? [] as $object) {
                $object->expired();
                $productQuoteObjectRefundRepository->save($object);
            }

            foreach ($productQuoteRefund->productQuoteOptionRefunds ?? [] as $option) {
                $option->expired();
                $productQuoteOptionRefundRepository->save($option);
            }

            $flightRequest->statusToExpired();
            $flightRequest->save(false);
        } catch (Throwable $e) {
            if (!empty($flightRequest)) {
                $flightRequest->statusToError();
                $flightRequest->save(false);
            }

            Yii::error([
                'product_quote_refund_gid' => $productQuoteRefund->pqr_gid ?? null,
                'trace' => AppHelper::throwableLog($e, true)
            ], 'VoluntaryRefundExpiredJob::execute');
        }
    }
}

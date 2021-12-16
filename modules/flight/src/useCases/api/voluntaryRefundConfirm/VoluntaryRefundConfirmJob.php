<?php

namespace modules\flight\src\useCases\api\voluntaryRefundConfirm;

use common\components\jobs\BaseJob;
use common\components\purifier\Purifier;
use common\models\Notifications;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\api\voluntaryRefundCreate\VoluntaryRefundCodeException;
use modules\order\src\entities\orderRefund\OrderRefundRepository;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefundRepository;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\helpers\app\AppHelper;
use sales\repositories\cases\CasesRepository;
use yii\queue\JobInterface;

/**
 * Class VoluntaryRefundConfirmJob
 * @package modules\flight\src\useCases\api\voluntaryRefundConfirm
 *
 * @property-read int $flightRequestId
 * @property-read int $productQuoteRefundId
 * @property-read ProductQuoteRefundRepository $productQuoteRefundRepository
 * @property-read ProductQuoteObjectRefundRepository $productQuoteObjectRefundRepository
 * @property-read ProductQuoteOptionRefundRepository $productQuoteOptionRefundRepository
 * @property-read OrderRefundRepository $orderRefundRepository
 * @property-read CasesRepository $caseRepository
 * @property-read string $productQuoteRefundCid
 */
class VoluntaryRefundConfirmJob extends BaseJob implements JobInterface
{
    private int $flightRequestId;
    private int $productQuoteRefundId;

    private ProductQuoteRefundRepository $productQuoteRefundRepository;
    private ProductQuoteObjectRefundRepository $productQuoteObjectRefundRepository;
    private ProductQuoteOptionRefundRepository $productQuoteOptionRefundRepository;
    private OrderRefundRepository $orderRefundRepository;
    private CasesRepository $caseRepository;
    private string $productQuoteRefundCid;

    public function __construct(
        int $flightRequestId,
        int $productQuoteRefundId,
        string $productQuoteRefundCid,
        ?float $timeStart = null,
        $config = []
    ) {
        parent::__construct($timeStart, $config);
        $this->flightRequestId = $flightRequestId;
        $this->productQuoteRefundId = $productQuoteRefundId;
        $this->productQuoteRefundCid = $productQuoteRefundCid;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->productQuoteRefundRepository = \Yii::createObject(ProductQuoteRefundRepository::class);
        $this->productQuoteObjectRefundRepository = \Yii::createObject(ProductQuoteObjectRefundRepository::class);
        $this->productQuoteOptionRefundRepository = \Yii::createObject(ProductQuoteOptionRefundRepository::class);
        $this->orderRefundRepository = \Yii::createObject(OrderRefundRepository::class);
        $this->caseRepository = \Yii::createObject(CasesRepository::class);
        $this->waitingTimeRegister();

        if (!$flightRequest = FlightRequest::findOne($this->flightRequestId)) {
            throw new \DomainException('FlightRequest not found, ID (' . $this->flightRequestId . ')');
        }

        try {
            $productQuoteRefund = $this->productQuoteRefundRepository->find($this->productQuoteRefundId);

            $case = $productQuoteRefund->case;

            $orderRefund = $productQuoteRefund->orderRefund;
            if ($orderRefund) {
                $orderRefund->processing();
                $orderRefund->detachBehavior('user');
                $this->orderRefundRepository->save($orderRefund);
            }


            $productQuoteRefund->pqr_cid = $this->productQuoteRefundCid;
            $productQuoteRefund->detachBehavior('user');
            $productQuoteRefund->processing();
            $this->productQuoteRefundRepository->save($productQuoteRefund);

            $productQuoteRefundObjects = $productQuoteRefund->productQuoteObjectRefunds;
            foreach ($productQuoteRefundObjects ?? [] as $productQuoteObjectRefund) {
                $productQuoteObjectRefund->detachBehavior('user');
                $productQuoteObjectRefund->pending();
                $this->productQuoteObjectRefundRepository->save($productQuoteObjectRefund);
            }

            $productQuoteRefundOptions = $productQuoteRefund->productQuoteOptionRefunds;
            foreach ($productQuoteRefundOptions ?? [] as $productQuoteOptionRefund) {
                $productQuoteOptionRefund->detachBehavior('user');
                $productQuoteOptionRefund->pending();
                $this->productQuoteOptionRefundRepository->save($productQuoteOptionRefund);
            }

            if ($case) {
                $case->awaiting(null, 'Product Quote Refund initiated');
                $this->caseRepository->save($case);
                $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_CONFIRM, 'Refund set to processing', null, CaseEventLog::CATEGORY_INFO);

                if ($case->cs_user_id) {
                    Notifications::createAndPublish(
                        $case->cs_user_id,
                        'Refund update',
                        'Refund(' . $productQuoteRefund->pqr_gid . ') for Case(' . Purifier::createCaseShortLink($case) . ') has been updated. Status: ' . ProductQuoteRefundStatus::getName($productQuoteRefund->pqr_status_id),
                        Notifications::TYPE_INFO,
                        true
                    );
                }
            }

            $flightRequest->statusToDone();
            $flightRequest->save();
        } catch (VoluntaryRefundCodeException $e) {
            $this->errorHandler($case ?? null, $productQuoteRefund ?? null, $e->getMessage(), null);
            $flightRequest->statusToError();
            $flightRequest->save();
        } catch (\Throwable $e) {
            $this->errorHandler($case ?? null, $productQuoteRefund ?? null, 'Server error: check system logs', $e);
            $flightRequest->statusToError();
            $flightRequest->save();
        }
    }

    private function errorHandler(
        ?Cases $case,
        ?ProductQuoteRefund $productQuoteRefund,
        ?string $description,
        ?\Throwable $e
    ): void {
        if ($case) {
            $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_CONFIRM, $description, null, CaseEventLog::CATEGORY_ERROR);
            $case->offIsAutomate()->error(null, $description);
            $this->caseRepository->save($case);
        }

        if ($e) {
            \Yii::error([
                'case_gid' => $case->cs_gid,
                'product_quote_refund_gid' => $productQuoteRefund->pqr_gid ?? null,
                'trace' => AppHelper::throwableLog($e, true)
            ], 'VoluntaryRefundCreateJob::errorHandler');
        }
    }
}

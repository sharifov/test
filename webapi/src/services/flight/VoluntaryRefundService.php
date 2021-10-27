<?php

namespace webapi\src\services\flight;

use common\components\hybrid\HybridWhData;
use common\models\Notifications;
use common\models\Project;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundQuery;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use sales\entities\cases\CaseEventLog;
use sales\interfaces\BoWebhookService;
use sales\repositories\cases\CasesRepository;
use sales\repositories\NotFoundException;
use webapi\src\forms\boWebhook\VoluntaryRefundUpdateForm;
use yii\base\Model;

/**
 * Class VoluntaryRefundService
 * @package webapi\src\services\flight
 *
 * @property-read ProductQuoteRefundRepository $productQuoteRefundRepository
 * @property-read CasesRepository $casesRepository
 * @property-read ProductQuoteRepository $productQuoteRepository
 */
class VoluntaryRefundService implements BoWebhookService
{
    private ProductQuoteRefundRepository $productQuoteRefundRepository;
    private CasesRepository $casesRepository;
    private ProductQuoteRepository $productQuoteRepository;

    public function __construct(
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        CasesRepository $casesRepository,
        ProductQuoteRepository $productQuoteRepository
    ) {
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
        $this->casesRepository = $casesRepository;
        $this->productQuoteRepository = $productQuoteRepository;
    }

    /**
     * @param VoluntaryRefundUpdateForm $form
     */
    public function processRequest(Model $form): void
    {
        $productQuoteRefund = ProductQuoteRefundQuery::getByBookingId($form->booking_id);
        if (!$productQuoteRefund) {
            throw new NotFoundException('Product Quote Refund not found by bookingId: ' . $form->booking_id);
        }

        if (!$project = Project::findOne(['api_key' => $form->project_key])) {
            throw new NotFoundException('Not found project by project api key: ' . $form->project_key);
        }

        $productQuote = $productQuoteRefund->productQuote;
        $case = $productQuoteRefund->case;
        if ($form->isProcessing()) {
            if (!$productQuoteRefund->isInProcessing() && !$productQuoteRefund->isDone()) {
                $productQuoteRefund->processing();
                $this->productQuoteRefundRepository->save($productQuoteRefund);
                $description = 'Refund set to processing on wh by bo with status processing';
                \Yii::warning([
                    'message' => $description,
                    'inputData' => $form->toArray(),
                    'productQuoteRefundGid' => $productQuoteRefund->pqr_gid,
                    'productQuoteGid' => $productQuote->pq_gid
                ], 'VoluntaryRefundService::processRequest');
                if ($case) {
                    $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_WH_UPDATE, $description);
                }
            }
        } elseif ($form->isRefunded()) {
            $productQuoteRefund->done();
            $this->productQuoteRefundRepository->save($productQuoteRefund);

            if ($case) {
                $case->solved(null, 'Refund complete (WH BO)');
                $this->casesRepository->save($case);
                $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_WH_UPDATE, 'Refund is completed (WH BO)');
            }

            $productQuote->cancelled();
            $this->productQuoteRepository->save($productQuote);
        } elseif ($form->isCanceled()) {
            $productQuoteRefund->error();
            $this->productQuoteRefundRepository->save($productQuoteRefund);

            if ($case) {
                $case->error(null, 'Refund canctypeeled (WH BO)');
                $this->casesRepository->save($case);
                $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_WH_UPDATE, 'Refund is canceled (WH BO)');
            }
        }

        $whData = HybridWhData::getData(HybridWhData::WH_TYPE_VOLUNTARY_REFUND_UPDATE);
        $whData['booking_id'] = $form->booking_id;
        $whData['product_quote_gid'] = $productQuote->pq_gid;
        $whData['refund_gid'] = $productQuoteRefund->pqr_gid;
        $whData['refund_status_id'] = $productQuoteRefund->pqr_status_id;
        \Yii::$app->hybrid->wh($project->id, HybridWhData::WH_TYPE_VOLUNTARY_REFUND_UPDATE, ['data' => $whData]);

        if ($case) {
            Notifications::createAndPublish(
                $case->cs_user_id,
                'Refund update',
                'Refund(' . $productQuoteRefund->pqr_gid . ') for Case(' . $case->cs_gid . ') has been updated. Status: ' . ProductQuoteRefundStatus::getName($productQuoteRefund->pqr_status_id),
                Notifications::TYPE_INFO,
                true
            );
        }
    }
}

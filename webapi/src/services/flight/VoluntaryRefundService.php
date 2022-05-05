<?php

namespace webapi\src\services\flight;

use common\components\hybrid\HybridWhData;
use common\components\purifier\Purifier;
use common\models\Notifications;
use common\models\Project;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundQuery;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use src\entities\cases\CaseEventLog;
use src\interfaces\BoWebhookService;
use src\repositories\cases\CasesRepository;
use src\repositories\NotFoundException;
use src\services\cases\CasesCommunicationService;
use webapi\src\forms\boWebhook\VoluntaryRefundUpdateForm;
use yii\base\Model;

/**
 * Class VoluntaryRefundService
 * @package webapi\src\services\flight
 *
 * @property-read ProductQuoteRefundRepository $productQuoteRefundRepository
 * @property-read CasesRepository $casesRepository
 * @property-read ProductQuoteRepository $productQuoteRepository
 * @property-read CasesCommunicationService $casesCommunicationService
 */
class VoluntaryRefundService implements BoWebhookService
{
    private ProductQuoteRefundRepository $productQuoteRefundRepository;
    private CasesRepository $casesRepository;
    private ProductQuoteRepository $productQuoteRepository;
    private CasesCommunicationService $casesCommunicationService;

    public function __construct(
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        CasesRepository $casesRepository,
        ProductQuoteRepository $productQuoteRepository,
        CasesCommunicationService $casesCommunicationService
    ) {
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
        $this->casesRepository = $casesRepository;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesCommunicationService = $casesCommunicationService;
    }

    /**
     * @param VoluntaryRefundUpdateForm $form
     */
    public function processRequest(Model $form): void
    {
        $productQuoteRefund = ProductQuoteRefundQuery::getByBookingIdAndCid($form->booking_id, $form->orderId);
        if (!$productQuoteRefund) {
            throw new NotFoundException('Product Quote Refund not found by bookingId: ' . $form->booking_id . ' and cid' . $form->orderId);
        }

        if (!$project = Project::findOne(['project_key' => $form->project_key])) {
            throw new NotFoundException('Not found project by project key: ' . $form->project_key);
        }

        $productQuoteRefund->detachBehavior('user');
        $productQuote = $productQuoteRefund->productQuote;
        $case = $productQuoteRefund->case;
        if ($form->isProcessing()) {
            if (!$productQuoteRefund->isInProcessing() && !$productQuoteRefund->isCompleted()) {
                $productQuoteRefund->processing();
                $this->productQuoteRefundRepository->save($productQuoteRefund);
                $description = 'Refund set to processing ';
                if ($case) {
                    $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_WH_UPDATE, $description, [], CaseEventLog::CATEGORY_INFO);
                }
            }
        } elseif ($form->isRefunded()) {
            $productQuoteRefund->complete();
            $this->productQuoteRefundRepository->save($productQuoteRefund);

            if ($case) {
                $case->solved(null, 'Refund complete (WH BO)');
                $this->casesRepository->save($case);
                $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_WH_UPDATE, 'Refund is completed (WH BO)', [], CaseEventLog::CATEGORY_INFO);
            }

            $productQuote->cancelled();
            $this->productQuoteRepository->save($productQuote);
            if ($case->project->getParams()->object->case->sendFeedback ?? null) {
                $this->casesCommunicationService
                    ->sendAutoFeedbackEmail($case, CaseEventLog::VOLUNTARY_REFUND_WH_UPDATE)
                ;
            }
        } elseif ($form->isCanceled()) {
            $productQuoteRefund->declined();
            $this->productQuoteRefundRepository->save($productQuoteRefund);

            if ($case) {
                $case->error(null, 'Refund canceled (WH BO)');
                $this->casesRepository->save($case);
                $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_WH_UPDATE, 'Refund is canceled (WH BO)', [], CaseEventLog::CATEGORY_INFO);
            }
        }

        $whData = HybridWhData::getData(HybridWhData::WH_TYPE_VOLUNTARY_REFUND_UPDATE);
        $whData['booking_id'] = $form->booking_id;
        $whData['product_quote_gid'] = $productQuote->pq_gid;
        $whData['refund_gid'] = $productQuoteRefund->pqr_gid;
        $whData['refund_order_id'] = $productQuoteRefund->pqr_cid;
        $whData['refund_status'] = ProductQuoteRefundStatus::getClientKeyStatusById($productQuoteRefund->pqr_status_id);

        if ($case && $case->cs_user_id) {
            Notifications::createAndPublish(
                $case->cs_user_id,
                'Refund update',
                'Refund(' . $productQuoteRefund->pqr_gid . ') for Case(' . Purifier::createCaseShortLink($case) . ') has been updated. Status: ' . ProductQuoteRefundStatus::getName($productQuoteRefund->pqr_status_id),
                Notifications::TYPE_INFO,
                true
            );
        }

        \Yii::info([
            'type' => HybridWhData::WH_TYPE_VOLUNTARY_REFUND_UPDATE,
            ['data' => $whData, 'caseUserId' => $case->cs_user_id ?? null]
        ], 'info\Webhook::OTA::VoluntaryRefund');
        \Yii::$app->hybrid->wh($project->id, HybridWhData::WH_TYPE_VOLUNTARY_REFUND_UPDATE, ['data' => $whData]);
    }
}

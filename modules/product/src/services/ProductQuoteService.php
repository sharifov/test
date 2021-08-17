<?php

namespace modules\product\src\services;

use common\models\Currency;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use sales\entities\cases\CaseEventLog;
use sales\interfaces\BoWebhookService;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesManageService;
use webapi\src\forms\boWebhook\ReprotectionUpdateForm;
use Yii;
use yii\base\Model;

/**
 * Class ProductQuoteService
 * @package modules\product\src\services
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property CasesManageService $casesManageService
 * @property CasesRepository $casesRepository
 */
class ProductQuoteService implements BoWebhookService
{
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;
    /**
     * @var CasesManageService
     */
    private CasesManageService $casesManageService;
    /**
     * @var CasesRepository
     */
    private CasesRepository $casesRepository;

    /**
     * ProductQuoteService constructor.
     * @param ProductQuoteRepository $productQuoteRepository
     * @param CasesManageService $casesManageService
     * @param CasesRepository $casesRepository
     */
    public function __construct(ProductQuoteRepository $productQuoteRepository, CasesManageService $casesManageService, CasesRepository $casesRepository)
    {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesManageService = $casesManageService;
        $this->casesRepository = $casesRepository;
    }

    /**
     * @param ProductQuote $productQuote
     * @param Currency $clientCurrency
     */
    public function recountProductQuoteClientPrice(ProductQuote $productQuote, Currency $clientCurrency): void
    {
        $productQuote->recountClientPrice($clientCurrency);
        $this->productQuoteRepository->save($productQuote);
    }

    public function detachProductQuoteFromOrder(ProductQuote $productQuote): void
    {
        if ($productQuote->isInProgress() || $productQuote->isPending()) {
            $productQuote->declined();
        }
        $productQuote->pq_order_id = null;
        $this->productQuoteRepository->save($productQuote);
    }

    /**
     * @param ReprotectionUpdateForm $form
     * @return void
     */
    public function processRequest(Model $form): void
    {
        $productQuote = $this->productQuoteRepository->findByGidFlightProductQuote($form->reprotection_quote_gid);
        if ($productQuote->isInProgress()) {
            $productQuote->booked();
            $this->productQuoteRepository->save($productQuote);

            $pqChange = $productQuote->relateParent->productQuoteLastChange;
            $pqChange->pqc_status_id = ProductQuoteChangeStatus::COMPLETE;
            $pqChange->save();

            $case = $productQuote->relateParent->productQuoteLastChange->pqcCase;
            $case->cs_is_automate = false;
            $case->addEventLog(CaseEventLog::CASE_AUTO_PROCESSING_MARK, 'Case auto processing: disabled');
            $this->casesManageService->solved($case, null, 'system: Bo Webhook');
            $this->casesRepository->save($case);
        }
    }
}

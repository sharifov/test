<?php

namespace modules\product\src\services;

use common\models\Currency;
use modules\flight\models\FlightQuoteFlight;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\flight\src\repositories\flightQuoteFlight\FlightQuoteFlightRepository;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use src\entities\cases\CaseEventLog;
use src\helpers\app\AppHelper;
use src\interfaces\BoWebhookService;
use src\repositories\cases\CasesRepository;
use src\services\cases\CasesManageService;
use webapi\src\forms\boWebhook\ReprotectionUpdateForm;
use Yii;
use yii\base\Model;

/**
 * Class ProductQuoteService
 * @package modules\product\src\services
 *
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuoteFlightRepository $flightQuoteFlightRepository
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
     * @var FlightQuoteFlightRepository
     */
    private FlightQuoteFlightRepository $flightQuoteFlightRepository;
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
     * @param FlightQuoteFlightRepository $flightQuoteFlightRepository
     */
    public function __construct(ProductQuoteRepository $productQuoteRepository, CasesManageService $casesManageService, CasesRepository $casesRepository, FlightQuoteFlightRepository $flightQuoteFlightRepository)
    {
        $this->productQuoteRepository = $productQuoteRepository;
        $this->casesManageService = $casesManageService;
        $this->casesRepository = $casesRepository;
        $this->flightQuoteFlightRepository = $flightQuoteFlightRepository;
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
        try {
            $productQuote = $this->productQuoteRepository->findByGidFlightProductQuote($form->reprotection_quote_gid);

            $flightOrigin = FlightQuoteFlight::find()->andWhere(['fqf_booking_id' => $form->booking_id])->orderBy(['fqf_id' => SORT_DESC])->one();
            $flightReprotection = $productQuote->flightQuote->flightQuoteFlight;

            if ($flightOrigin && $flightReprotection) {
                $flightReprotection->fqf_booking_id = $flightOrigin->fqf_booking_id;

                $flightOrigin->fqf_booking_id = null;
                $this->flightQuoteFlightRepository->save($flightOrigin);
                $this->flightQuoteFlightRepository->save($flightReprotection);
            }

            if ($productQuote->isInProgress()) {
                $productQuote->booked();
                $this->productQuoteRepository->save($productQuote);

                if (!$pqChange = $productQuote->productQuoteChangeLastRelation->pqcrPqc ?? null) {
                    throw new \RuntimeException('productQuoteChange not found');
                }

                $pqChange->pqc_status_id = ProductQuoteChangeStatus::COMPLETED;
                $pqChange->save();

                $case = $pqChange->pqcCase;
                $case->cs_is_automate = false;
                $case->addEventLog(CaseEventLog::CASE_AUTO_PROCESSING_MARK, 'Case auto processing: disabled');
                $this->casesManageService->solved($case, null, 'Reprotection flight quote booked');

                if ($case->isNeedAction()) {
                    $case->offNeedAction();
                }
                $this->casesRepository->save($case);

                $this->removeRelationsProcessing($productQuote);
            }
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['data'] = $form->toArray();
            Yii::error(
                $message,
                'ProductQuoteService:processRequest:Throwable'
            );
        }
    }

    private function removeRelationsProcessing(
        ProductQuote $reProtectionQuote
    ): void {
        ProductQuoteRelation::deleteAll([
            'pqr_related_pq_id' => $reProtectionQuote->pq_id,
            'pqr_type_id' => ProductQuoteRelation::TYPE_REPROTECTION
        ]);
        ProductQuoteChangeRelation::deleteAll(['pqcr_pq_id' => $reProtectionQuote->pq_id]);
    }
}

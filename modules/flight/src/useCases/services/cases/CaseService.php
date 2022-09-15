<?php

namespace modules\flight\src\useCases\services\cases;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use src\entities\cases\Cases;
use src\model\caseOrder\entity\CaseOrder;

/**
 * Class CaseService
 */
class CaseService
{
    /**
     * @param ProductQuoteChange $productQuoteChange
     * @param ProductQuote $productQuote
     * @return Cases|null
     */
    public function getCase(ProductQuoteChange $productQuoteChange, ProductQuote $productQuote): ?Cases
    {
        $case = $this->getCaseByStatusAndProductQuote($productQuote);
        if (!empty($case)) {
            return $case;
        }
        return $productQuoteChange->pqcCase ?? null;
    }

    /**
     * @param ProductQuoteRefund $productQuoteRefund
     * @param ProductQuote $productQuote
     * @return Cases|null
     */
    public function getCaseByProductQuoteRefund(ProductQuoteRefund $productQuoteRefund, ProductQuote $productQuote): ?Cases
    {
        $case = $this->getCaseByStatusAndProductQuote($productQuote);
        if (!empty($case)) {
            return $case;
        }
        return $productQuoteRefund->case ?? null;
    }

    /**
     * @param ProductQuote $productQuote
     * @return Cases|null
     */
    public function getCaseByStatusAndProductQuote(ProductQuote $productQuote): ?Cases
    {
        $order = $productQuote->pqOrder;
        if ($order) {
            $orderCases = $order->getCaseOrder()->orderBy(['co_case_id' => SORT_DESC])->all();
            if ($orderCases) {
                foreach ($orderCases as $orderCase) {
                    /** @var CaseOrder $orderCase */
                    $case = $orderCase->getCases()->one();
                    /** @var Cases $case */
                    if ($case && ($case->isProcessing() || $case->isStatusAutoProcessing())) {
                        return $case;
                    }
                }
            }
        }
        return null;
    }
}

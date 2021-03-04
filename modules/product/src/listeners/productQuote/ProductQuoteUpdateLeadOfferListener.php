<?php

namespace modules\product\src\listeners\productQuote;

use common\models\Notifications;
use modules\product\src\entities\productQuote\events\ProductQuotable;
use modules\product\src\entities\productQuote\ProductQuote;

class ProductQuoteUpdateLeadOfferListener
{
    public function handle(ProductQuotable $event): void
    {
        $quote = ProductQuote::findOne($event->getProductQuoteId());

        if (!$quote) {
            return;
        }

        $leadId = $quote->pqOrder->or_lead_id ?? null;

        if (!$leadId) {
            return;
        }

        try {
            Notifications::pub(
                ['lead-' . $leadId],
                'reloadOffers',
                ['data' => []]
            );
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage(), 'ProductQuoteUpdateLeadOfferListener');
        }
    }
}

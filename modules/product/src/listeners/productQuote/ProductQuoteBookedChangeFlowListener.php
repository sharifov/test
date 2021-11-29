<?php

namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteBookedChangeFlowEvent;
use modules\product\src\entities\productQuoteStatusLog\CreateDto;
use modules\product\src\services\ProductQuoteStatusLogService;
use sales\helpers\app\AppHelper;
use Yii;

/**
 * @property ProductQuoteStatusLogService $productQuoteStatusLogService
 */
class ProductQuoteBookedChangeFlowListener
{
    private ProductQuoteStatusLogService $productQuoteStatusLogService;

    /**
     * @param ProductQuoteStatusLogService $productQuoteStatusLogService
     */
    public function __construct(ProductQuoteStatusLogService $productQuoteStatusLogService)
    {
        $this->productQuoteStatusLogService = $productQuoteStatusLogService;
    }

    /**
     * @param ProductQuoteBookedChangeFlowEvent $event
     */
    public function handle(ProductQuoteBookedChangeFlowEvent $event): void
    {
        try {
            $this->productQuoteStatusLogService->log(new CreateDto(
                $event->productQuoteId,
                $event->startStatusId,
                $event->endStatusId,
                $event->description,
                null,
                $event->ownerId,
                $event->creatorId
            ));
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable),
                'ProductQuoteBookedChangeFlowListener:Throwable'
            );
        }
    }
}

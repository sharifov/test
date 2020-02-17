<?php

namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteDeclinedEvent;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteStatusLog\CreateDto;
use modules\product\src\services\ProductQuoteStatusLogService;
use sales\helpers\app\AppHelper;
use Yii;

/**
 *
 * @property ProductQuoteStatusLogService $productQuoteStatusLogService
 */
class ProductQuoteDeclinedEventListener
{
    private $productQuoteStatusLogService;

    /**
     * ProductQuoteDeclinedEventListener constructor.
     * @param ProductQuoteStatusLogService $productQuoteStatusLogService
     */
    public function __construct(ProductQuoteStatusLogService $productQuoteStatusLogService)
    {
        $this->productQuoteStatusLogService = $productQuoteStatusLogService;
    }

    /**
     * @param ProductQuoteDeclinedEvent $event
     */
    public function handle(ProductQuoteDeclinedEvent $event): void
    {
        try {
            $this->productQuoteStatusLogService->log(new CreateDto(
                $event->productQuoteId,
                $event->startStatusId,
                ProductQuoteStatus::DECLINED,
                $event->description,
                null,
                $event->ownerId,
                $event->creatorId
            ));
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }

}
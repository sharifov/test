<?php

namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteErrorEvent;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteStatusLog\CreateDto;
use modules\product\src\services\ProductQuoteStatusLogService;
use sales\helpers\app\AppHelper;
use Yii;

/**
 *
 * @property ProductQuoteStatusLogService $productQuoteStatusLogService
 */
class ProductQuoteErrorEventListener
{
    private $productQuoteStatusLogService;

    public function __construct(ProductQuoteStatusLogService $productQuoteStatusLogService)
    {
        $this->productQuoteStatusLogService = $productQuoteStatusLogService;
    }

    /**
     * @param ProductQuoteErrorEvent $event
     */
    public function handle(ProductQuoteErrorEvent $event): void
    {
        try {
            $this->productQuoteStatusLogService->log(new CreateDto(
                $event->productQuoteId,
                $event->startStatusId,
                ProductQuoteStatus::ERROR,
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
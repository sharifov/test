<?php

namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteStatusChangeEvent;
use modules\product\src\entities\productQuoteStatusLog\CreateDto;
use modules\product\src\services\ProductQuoteStatusLogService;
use sales\helpers\app\AppHelper;
use Yii;

/**
 *
 * @property ProductQuoteStatusLogService $productQuoteStatusLogService
 */
class ProductQuoteStatusChangeEventListener
{
    private $productQuoteStatusLogService;

    public function __construct(ProductQuoteStatusLogService $productQuoteStatusLogService)
    {
        $this->productQuoteStatusLogService = $productQuoteStatusLogService;
    }

    /**
     * @param ProductQuoteStatusChangeEvent $event
     */
    public function handle(ProductQuoteStatusChangeEvent $event): void
    {
        try {
            $this->productQuoteStatusLogService->log(new CreateDto(
                $event->productQuoteId,
                $event->startStatusId,
                $event->endStatusId,
                $event->description,
                $event->actionId,
                $event->ownerId,
                $event->creatorId
            ));
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }

}
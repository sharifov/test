<?php

namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteStatusChangeInterface;
use modules\product\src\entities\productQuoteStatusLog\CreateDto;
use modules\product\src\services\ProductQuoteStatusLogService;
use src\helpers\app\AppHelper;
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
     * @param ProductQuoteStatusChangeInterface $event
     */
    public function handle(ProductQuoteStatusChangeInterface $event): void
    {
        try {
            $this->productQuoteStatusLogService->log(new CreateDto(
                $event->getId(),
                $event->getStartStatus(),
                $event->getEndStatus(),
                $event->getDescription(),
                $event->getActionId(),
                $event->getOwnerId(),
                $event->getCreatorId()
            ));
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}

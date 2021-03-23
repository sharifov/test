<?php

namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteCanceledEvent;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use modules\product\src\entities\productQuoteStatusLog\CreateDto;
use modules\product\src\services\ProductQuoteStatusLogService;
use sales\helpers\app\AppHelper;
use Yii;

/**
 *
 * @property ProductQuoteStatusLogService $productQuoteStatusLogService
 * @property ProductQuoteOptionRepository $productQuoteOptionRepository
 */
class ProductQuoteCanceledEventListener
{
    private $productQuoteStatusLogService;
    private ProductQuoteOptionRepository $productQuoteOptionRepository;

    public function __construct(
        ProductQuoteStatusLogService $productQuoteStatusLogService,
        ProductQuoteOptionRepository $productQuoteOptionRepository
    ) {
        $this->productQuoteStatusLogService = $productQuoteStatusLogService;
        $this->productQuoteOptionRepository = $productQuoteOptionRepository;
    }

    /**
     * @param ProductQuoteCanceledEvent $event
     */
    public function handle(ProductQuoteCanceledEvent $event): void
    {
        try {
            $this->productQuoteStatusLogService->log(new CreateDto(
                $event->productQuoteId,
                $event->startStatusId,
                ProductQuoteStatus::CANCELED,
                $event->description,
                null,
                $event->ownerId,
                $event->creatorId
            ));

            if (
                ($productQuote = ProductQuote::findOne($event->productQuoteId)) &&
                $productQuote->isFlight() &&
                count($productQuote->productQuoteOptions)
            ) {
                foreach ($productQuote->productQuoteOptions as $option) {
                    $option->canceled();
                    $this->productQuoteOptionRepository->save($option);
                }
            }
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}

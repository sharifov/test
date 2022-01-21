<?php

namespace modules\product\src\listeners\productQuote;

use modules\product\src\entities\productQuote\events\ProductQuoteInProgressEvent;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use modules\product\src\entities\productQuoteStatusLog\CreateDto;
use modules\product\src\services\ProductQuoteStatusLogService;
use src\helpers\app\AppHelper;
use Yii;

/**
 *
 * @property ProductQuoteStatusLogService $productQuoteStatusLogService
 * @property ProductQuoteOptionRepository $productQuoteOptionRepository
 */
class ProductQuoteInProgressEventListener
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
     * @param ProductQuoteInProgressEvent $event
     */
    public function handle(ProductQuoteInProgressEvent $event): void
    {
        try {
            $this->productQuoteStatusLogService->log(new CreateDto(
                $event->productQuoteId,
                $event->startStatusId,
                ProductQuoteStatus::IN_PROGRESS,
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
                    $option->inProgress();
                    $this->productQuoteOptionRepository->save($option);
                }
            }
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableFormatter($e), 'Listeners:' . self::class);
        }
    }
}

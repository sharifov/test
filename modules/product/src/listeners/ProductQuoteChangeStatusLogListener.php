<?php

namespace modules\product\src\listeners;

use modules\product\src\entities\productQuoteStatusLog\CreateDto;
use Yii;
use modules\product\src\services\ProductQuoteStatusLogService;
use modules\product\src\entities\productQuote\events\ProductQuoteStatusChangeInterface;

/**
 * Class ProductQuoteChangeStatusLogListener
 *
 * @property ProductQuoteStatusLogService $logger
 */
class ProductQuoteChangeStatusLogListener
{
    private $logger;

    public function __construct(ProductQuoteStatusLogService $logger)
    {
        $this->logger = $logger;
    }

    public function handle(ProductQuoteStatusChangeInterface $event): void
    {
        try {
            $this->logger->log(new CreateDto(
                $event->getId(),
                $event->getStartStatus(),
                $event->getEndStatus(),
                $event->getDescription(),
                $event->getActionId(),
                $event->getOwnerId(),
                $event->getCreatorId()
            ));
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:ProductQuoteChangeStatusLogListener');
        }
    }
}

<?php

namespace modules\product\src\services;

use modules\product\src\entities\productQuoteStatusLog\CreateDto;
use modules\product\src\entities\productQuoteStatusLog\ProductQuoteStatusLog;
use modules\product\src\entities\productQuoteStatusLog\ProductQuoteStatusLogRepository;

/**
 * Class ProductQuoteStatusLogService
 *
 * @property ProductQuoteStatusLogRepository $repository
 */
class ProductQuoteStatusLogService
{
    private $repository;

    public function __construct(ProductQuoteStatusLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function log(CreateDto $dto): void
    {
        if ($previous = $this->repository->getPrevious($dto->productQuoteId)) {
            $previous->end();
            $this->repository->save($previous);
        }
        $log = ProductQuoteStatusLog::create($dto);
        $this->repository->save($log);
    }
}

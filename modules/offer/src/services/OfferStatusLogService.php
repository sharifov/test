<?php

namespace modules\product\src\services;

use modules\offer\src\entities\offerStatusLog\CreateDto;
use modules\offer\src\entities\offerStatusLog\OfferStatusLog;
use modules\offer\src\entities\offerStatusLog\OfferStatusLogRepository;

/**
 * Class OfferStatusLogService
 *
 * @property OfferStatusLogRepository $repository
 */
class OfferStatusLogService
{
    private $repository;

    public function __construct(OfferStatusLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function log(CreateDto $dto): void
    {
        if ($previous = $this->repository->getPrevious($dto->offerId)) {
            $previous->end();
            $this->repository->save($previous);
        }
        $log = OfferStatusLog::create($dto);
        $this->repository->save($log);
    }
}

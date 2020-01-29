<?php

namespace modules\offer\src\services;

use modules\offer\src\entities\offerViewLog\CreateDto;
use modules\offer\src\entities\offerViewLog\OfferViewLog;
use modules\offer\src\entities\offerViewLog\OfferViewLogRepository;

/**
 * Class OfferViewLogService
 *
 * @property OfferViewLogRepository $repository
 */
class OfferViewLogService
{
    private $repository;

    public function __construct(OfferViewLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function log(CreateDto $dto): void
    {
        $log = OfferViewLog::create($dto);
        $this->repository->save($log);
    }
}

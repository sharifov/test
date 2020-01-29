<?php

namespace modules\offer\src\services;

use modules\offer\src\entities\offerSendLog\CreateDto;
use modules\offer\src\entities\offerSendLog\OfferSendLog;
use modules\offer\src\entities\offerSendLog\OfferSendLogRepository;

/**
 * Class OfferSendLogService
 *
 * @property OfferSendLogRepository $repository
 */
class OfferSendLogService
{
    private $repository;

    public function __construct(OfferSendLogRepository $repository)
    {
        $this->repository = $repository;
    }

    public function log(CreateDto $dto): void
    {
        $log = OfferSendLog::create($dto);
        $this->repository->save($log);
    }
}

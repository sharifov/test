<?php

namespace modules\offer\src\services;

use modules\offer\src\entities\offerSendLog\CreateDto;
use modules\offer\src\entities\offerSendLog\OfferSendLog;
use modules\offer\src\entities\offerSendLog\OfferSendLogRepository;
use yii\helpers\VarDumper;

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
        try {
            $this->repository->save($log);
        } catch (\Throwable $e) {
            \Yii::error($e . ' Dto: ' .  VarDumper::dumpAsString($dto), 'OfferSendLogService:save');
        }
    }
}

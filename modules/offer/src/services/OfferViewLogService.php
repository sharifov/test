<?php

namespace modules\offer\src\services;

use modules\offer\src\entities\offerViewLog\CreateDto;
use modules\offer\src\entities\offerViewLog\OfferViewLog;
use modules\offer\src\entities\offerViewLog\OfferViewLogRepository;
use yii\helpers\VarDumper;

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
        try {
            $this->repository->save($log);
        } catch (\Throwable $e) {
            \Yii::error($e . VarDumper::dumpAsString($dto), 'OfferViewLogService:log');
        }
    }
}

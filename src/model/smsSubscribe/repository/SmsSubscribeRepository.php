<?php

namespace src\model\smsSubscribe\repository;

use src\model\smsSubscribe\entity\SmsSubscribe;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * Class ClientDataRepository
 *
 * @property SmsSubscribe $model
 */
class SmsSubscribeRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param SmsSubscribe $model
     */
    public function __construct(SmsSubscribe $model)
    {
        parent::__construct($model);
    }

    public function getModel(): SmsSubscribe
    {
        return $this->model;
    }
}

<?php

namespace sales\model\smsSubscribe\repository;

use sales\model\smsSubscribe\entity\SmsSubscribe;
use sales\repositories\AbstractRepositoryWithEvent;

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

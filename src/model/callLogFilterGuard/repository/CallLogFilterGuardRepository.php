<?php

namespace src\model\callLogFilterGuard\repository;

use src\model\callLogFilterGuard\entity\CallLogFilterGuard;
use src\repositories\AbstractBaseRepository;

/**
 * Class CallLogFilterGuardRepository
 */
class CallLogFilterGuardRepository extends AbstractBaseRepository
{
    /**
     * @param CallLogFilterGuard $model
     */
    public function __construct(CallLogFilterGuard $model)
    {
        parent::__construct($model);
    }

    public function getModel(): CallLogFilterGuard
    {
        return $this->model;
    }
}

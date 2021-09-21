<?php

namespace sales\model\callLogFilterGuard\repository;

use sales\model\callLogFilterGuard\entity\CallLogFilterGuard;
use sales\repositories\AbstractBaseRepository;

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

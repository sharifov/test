<?php

namespace src\model\clientData\repository;

use src\model\clientData\entity\ClientData;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * Class ClientDataRepository
 *
 * @property ClientData $model
 */
class ClientDataRepository extends AbstractRepositoryWithEvent
{
    /**
     * @param ClientData $model
     */
    public function __construct(ClientData $model)
    {
        parent::__construct($model);
    }

    public function getModel(): ClientData
    {
        return $this->model;
    }
}

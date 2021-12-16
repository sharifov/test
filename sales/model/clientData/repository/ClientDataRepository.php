<?php

namespace sales\model\clientData\repository;

use sales\model\clientData\entity\ClientData;
use sales\repositories\AbstractRepositoryWithEvent;

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

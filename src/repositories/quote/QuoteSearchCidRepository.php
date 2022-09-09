<?php

namespace src\repositories\quote;

use common\models\QuoteSearchCid;
use src\repositories\AbstractRepositoryWithEvent;

/**
 * @property QuoteSearchCid $model
 */
class QuoteSearchCidRepository extends AbstractRepositoryWithEvent
{
    public function __construct(QuoteSearchCid $model)
    {
        parent::__construct($model);
    }

    public function getModel(): QuoteSearchCid
    {
        return $this->model;
    }
}

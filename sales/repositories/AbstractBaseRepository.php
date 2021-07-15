<?php

namespace sales\repositories;

use yii\db\ActiveRecord;
use sales\helpers\ErrorsToStringHelper;

/**
 * Class AbstractBaseRepository
 *
 * @property ActiveRecord $model
 */
abstract class AbstractBaseRepository
{
    protected ActiveRecord $model;

    /**
     * @param ActiveRecord $model
     */
    public function __construct(ActiveRecord $model)
    {
        $this->model = $model;
    }

    public function save(bool $runValidation = false): ActiveRecord
    {
        if (!$this->model->save($runValidation)) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($this->model));
        }
        return $this->model;
    }
}

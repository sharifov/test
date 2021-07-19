<?php

namespace sales\repositories;

use sales\helpers\ErrorsToStringHelper;
use yii\db\ActiveRecordInterface;

/**
 * Class AbstractBaseRepository
 *
 * @property $model
 */
abstract class AbstractBaseRepository
{
    protected $model;

    /**
     * @param ActiveRecordInterface $model
     */
    public function __construct(ActiveRecordInterface $model)
    {
        $this->model = $model;
    }

    public function save(bool $runValidation = false): AbstractBaseRepository
    {
        if (!$this->model->save($runValidation)) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($this->model));
        }
        return $this;
    }

    abstract public function getModel(): ActiveRecordInterface;
}

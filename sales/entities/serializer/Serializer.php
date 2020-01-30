<?php

namespace sales\entities\serializer;

use yii\db\ActiveRecord;

/**
 * Class ExtraData
 *
 * @property ActiveRecord $model
 */
abstract class Serializer
{
    protected $model;

    public function __construct(ActiveRecord $model)
    {
        $this->model = $model;
    }

    /** only these fields will be involved  */
    abstract public static function fields(): array;

    /** full data entity with all relations */
    abstract public function getData(): array;

    public function getShortData(): array
    {
        return $this->toArray();
    }

    /**
     * @return array the array representation of the object
     */
    protected function toArray(): array
    {
        return $this->model->toArray(static::fields());
    }
}

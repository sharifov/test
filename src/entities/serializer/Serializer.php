<?php

namespace src\entities\serializer;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
        return ArrayHelper::toArray($this->model, [
            get_class($this->model) => static::fields()
        ]);
    }
}

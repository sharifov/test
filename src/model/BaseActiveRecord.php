<?php

namespace src\model;

use src\exception\CreateModelException;
use src\exception\ModelNotFoundException;

/**
 * Class BaseActiveRecord
 */
class BaseActiveRecord extends \yii\db\ActiveRecord
{
    public static function newInstance(array $attributes = [])
    {
        $model = new static();
        if (!empty($attributes)) {
            $model->fill($attributes);
        }

        return $model;
    }

    public function fill(array $attributes)
    {
        if (method_exists($this, 'load')) {
            $this->load($attributes, '');
        }
        return $this;
    }

    public static function create(array $attributes)
    {
        $model = static::newInstance($attributes);
        if (!$model->save()) {
            throw new CreateModelException(get_class($model), $model->getErrors());
        }

        return $model;
    }

    /**
     * @param array $condition
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function findOneOrFail(array $condition)
    {
        if (is_null($model = static::find()->andWhere($condition)->limit(1)->one())) {
            throw new ModelNotFoundException(static::class);
        }

        return $model;
    }

    /**
     * @param array $attributes
     * @return array|BaseActiveRecord|\yii\db\ActiveRecord|null
     */
    public static function findOneOrNew(array $attributes)
    {
        $model = static::find()
            ->andWhere($attributes)
            ->limit(1)
            ->one();

        if (is_null($model)) {
            $model = new static($attributes);
        }

        return $model;
    }

    public static function count($condition)
    {
        return static::find()->andWhere($condition)->count();
    }
}

<?php

namespace modules\taskList\src\entities;

use yii\db\ActiveRecord;

/**
 * Class TargetObject
 */
class TargetObject
{
    public const TARGET_OBJ_LEAD = 'lead';

    public const TARGET_OBJ_LIST = [
        self::TARGET_OBJ_LEAD => self::TARGET_OBJ_LEAD,
    ];

    public const TARGET_OBJ_CLASS_LIST = [
        self::TARGET_OBJ_LEAD => \common\models\Lead::class,
    ];

    /**
     * @param string $objectName
     * @param int $targetId
     * @return ActiveRecord|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function getTargetObject(string $objectName, int $targetId): ?ActiveRecord
    {
        if (!array_key_exists($objectName, self::TARGET_OBJ_CLASS_LIST)) {
            throw new \RuntimeException('ObjectName(' . $objectName . ') is not valid');
        }
        if (!class_exists(self::TARGET_OBJ_CLASS_LIST[$objectName])) {
            throw new \RuntimeException('Class(' . self::TARGET_OBJ_CLASS_LIST[$objectName] . ') not found');
        }

        $model = \Yii::createObject(self::TARGET_OBJ_CLASS_LIST[$objectName]);
        if (!$model instanceof ActiveRecord) {
            throw new \RuntimeException('Object(' . $objectName . ') not instanceof ActiveRecord');
        }
        return $model::findOne($targetId);
    }
}

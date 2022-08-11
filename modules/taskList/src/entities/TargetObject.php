<?php

namespace modules\taskList\src\entities;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;

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

    public const TARGET_OBJ_LINK = [
        self::TARGET_OBJ_LEAD => ['tpl' => 'lead/view', 'allowed' => ['gid']]
    ];

    /**
     * @param string $objectName
     * @param int $targetId
     * @return ActiveRecord|null
     * @throws InvalidConfigException
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

    /**
     * @param string $objectName
     * @param int $targetId
     * @return string|null
     * @throws InvalidConfigException
     */
    public static function getTargetLink(string $objectName, int $targetId): ?string
    {
        $link = null;
        $object = self::getTargetObject($objectName, $targetId);
        if ($object) {
            if (array_key_exists($objectName, self::TARGET_OBJ_LINK)) {
                $tpl = self::TARGET_OBJ_LINK[$objectName]['tpl'];
                $allowed = self::TARGET_OBJ_LINK[$objectName]['allowed'];
                $attr = $object->attributes;
                $data = array_intersect_key($attr, array_flip($allowed));
                if ($data) {
                    $urlData[] = $tpl;
                    foreach ($data as $key => $item) {
                        $urlData[$key] = $item;
                    }
                    $link = Html::a($targetId, $urlData, ['target' => '_blank', 'data-pjax' => 0]);
                }
            }
        }
        return $link;
    }
}

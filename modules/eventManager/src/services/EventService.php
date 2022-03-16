<?php

namespace modules\eventManager\src\services;

use Cron\CronExpression;
use kivork\FeatureFlag\Components\FeatureFlagBaseModel;
use kivork\FeatureFlag\Models\FeatureFlag;
use modules\eventManager\src\entities\EventList;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;

class EventService
{
    /**
     * @param mixed|null $expression
     * @param $currentTime
     * @return bool
     */
    public static function isDueCronExpression(?string $expression, $currentTime = 'now'): bool
    {
        if ($expression === null) {
            return false;
        }
        $cron = CronExpression::factory($expression);
        return $cron->isDue($currentTime);
    }

    /**
     * @param int $enableTypeId
     * @return string
     */
    public static function getEnableTypeName(int $enableTypeId): string
    {
        return EventList::ET_LIST[$enableTypeId] ?? '';
    }

    /**
     * @param int $enableTypeId
     * @return string
     */
    public static function getEnableTypeClass(int $enableTypeId): string
    {
        return EventList::ET_CLASS_LIST[$enableTypeId] ?? '';
    }

    /**
     * @param int $enableTypeId
     * @return string
     */
    public static function getEnableTypeDesc(int $enableTypeId): string
    {
        return EventList::ET_DESC_LIST[$enableTypeId] ?? '';
    }

    /**
     * @param int $enableTypeId
     * @return string
     */
    public static function getEnableTypeLabel(int $enableTypeId): string
    {
        $class = self::getEnableTypeClass($enableTypeId);
        $name = self::getEnableTypeName($enableTypeId);
        return Html::tag('span', $name, ['class' => $class ? 'label label-' . $class : null]);
    }
}

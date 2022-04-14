<?php

namespace src\helpers;

use common\models\Notifications;
use yii\helpers\Html;

class NotificationsHelper
{
    private const ICON_LIST_CLASS = [
        Notifications::TYPE_SUCCESS => 'fa fa-check-circle',
        Notifications::TYPE_INFO => 'fa fa-info-circle',
        Notifications::TYPE_WARNING => 'fa fa-exclamation-triangle',
        Notifications::TYPE_DANGER => 'fa fa-times-circle-o'
    ];

    private const ICON_LIST_COLOR = [
        Notifications::TYPE_SUCCESS => 'text-success',
        Notifications::TYPE_INFO => 'text-info',
        Notifications::TYPE_WARNING => 'text-warning',
        Notifications::TYPE_DANGER => 'text-danger'
    ];

    public static function getIcon(int $id): string
    {
        return Html::tag('i', '', ['class' => self::getIconClass($id) . ' ' . self::getIconColor($id)]);
    }

    private static function getIconClass(int $id): string
    {
        return self::ICON_LIST_CLASS[$id] ?? '';
    }

    private static function getIconColor(int $id): string
    {
        return self::ICON_LIST_COLOR[$id] ?? '';
    }
}

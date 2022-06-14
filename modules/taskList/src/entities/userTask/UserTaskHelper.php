<?php

namespace modules\taskList\src\entities\userTask;

use yii\bootstrap4\Html;

/**
 * Class UserTaskHelper
 */
class UserTaskHelper
{
    public const STATUS_LIST_LABEL_MAP = [
        UserTask::STATUS_PROCESSING => 'info',
        UserTask::STATUS_COMPLETE => 'success',
        UserTask::STATUS_CANCEL => 'warning',
    ];

    public const PRIORITY_LIST_LABEL_MAP = [
        UserTask::PRIORITY_LOW => 'info',
        UserTask::PRIORITY_MEDIUM => 'success',
        UserTask::PRIORITY_HIGH => 'warning',
    ];

    public static function statusLabel(?int $statusId, string $fontSize = '11px'): string
    {
        if (!$statusId) {
            return \Yii::$app->formatter->nullDisplay;
        }

        return Html::tag(
            'span',
            Html::encode(UserTask::getStatusName($statusId)),
            [
                'class' => 'label label-' . self::getStatusLabelClass($statusId),
                'style' => 'font-size: ' . $fontSize . ';'
            ]
        );
    }

    public static function priorityLabel(?int $priorityId, string $fontSize = '11px'): string
    {
        if (!$priorityId) {
            return \Yii::$app->formatter->nullDisplay;
        }

        return Html::tag(
            'span',
            Html::encode(UserTask::getPriorityName($priorityId)),
            [
                'class' => 'label label-' . self::getPriorityLabelClass($priorityId),
                'style' => 'font-size: ' . $fontSize . ';'
            ]
        );
    }

    public static function getStatusLabelClass(?int $statusId): string
    {
        return self::STATUS_LIST_LABEL_MAP[$statusId] ?? 'default';
    }

    public static function getPriorityLabelClass(?int $priorityId): string
    {
        return self::PRIORITY_LIST_LABEL_MAP[$priorityId] ?? 'default';
    }
}

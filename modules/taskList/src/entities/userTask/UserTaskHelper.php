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
        UserTask::STATUS_FAILED => 'danger',
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

    public static function renderStatus(?int $status): string
    {
        switch ($status) {
            case UserTask::STATUS_COMPLETE:
                return '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
            case UserTask::STATUS_CANCEL:
                return '<i class="fa fa-times" aria-hidden="true"></i>';
            default:
                return '<i class="fa fa-square-o" aria-hidden="true"></i>';
        }
    }

    public static function getColorByStatus(?int $status): string
    {
        switch ($status) {
            case UserTask::STATUS_COMPLETE:
                return 'rgba(83, 162, 101, 0.1)';
            case UserTask::STATUS_CANCEL:
                return 'rgba(255, 192, 82, 0.1)';
            default:
                return 'none';
        }
    }

    public static function getStatusLabelClass(?int $statusId): string
    {
        return self::STATUS_LIST_LABEL_MAP[$statusId] ?? 'default';
    }

    public static function getPriorityLabelClass(?int $priorityId): string
    {
        return self::PRIORITY_LIST_LABEL_MAP[$priorityId] ?? 'default';
    }

    /**
     * @param string|null $startDt
     * @param string|null $endDt
     * @return string
     */
    public static function getDeadlineTimer(?string $startDt, ?string $endDt): string
    {
        $str = '-';

        $delay = false;
        if ($startDt) {
            if (time() < strtotime($startDt)) {
                $delay = true;

                return '-';
            }
        }

        if ($endDt) {
            $sec = strtotime($endDt) - time();


            if ($sec > 0) {
                $diffHours = (int) ($sec / (60 * 60));

                if ($diffHours > 23) {
                    $str = Html::tag(
                        'span',
                        \Yii::$app->formatter->asRelativeTime(strtotime($endDt)),
                        ['title' => \Yii::$app->formatter->asRelativeTime(strtotime($endDt)),
                            'class' => '']
                    );
                } else {
                    if ($diffHours < 2) {
                        $timerFormat = '%H:%M:%S';
                        $phpFormat = 'H:i:s';
                    } else {
                        $timerFormat = '%H:%M:%S';
                        $phpFormat = 'H:i:s';
                    }

                    $str = \yii\helpers\Html::tag(
                        'span',
                        gmdate($phpFormat, $sec),
                        ['class' => 'badge badge-warning timer',
                            'data-sec' => $sec,
                            'data-control' => 'start',
                            'data-format' => $timerFormat]
                    );
                }
            } else {
                $str = Html::tag(
                    'span',
                    'Deadline',
                    ['title' => \Yii::$app->formatter->asRelativeTime(strtotime($endDt)),
                        'class' => 'badge badge-danger']
                );
            }
        }
        return $str;
    }

    /**
     * @param string|null $startDt
     * @param string|null $endDt
     * @return string
     */
    public static function getDelayTimer(?string $startDt, ?string $endDt): string
    {
        $str = '-';
        if ($startDt) {
            $sec = strtotime($startDt) - time();
            $diffHours = (int) ($sec / (60 * 60));

            if ($sec > 0) {
                if ($diffHours > 23) {
                    $str = Html::tag(
                        'span',
                        \Yii::$app->formatter->asRelativeTime(strtotime($startDt)),
                        ['title' => \Yii::$app->formatter->asRelativeTime(strtotime($startDt)),
                            'class' => '']
                    );
                } else {
                    if ($diffHours < 2) {
                        $timerFormat = '%H:%M:%S';
                        $phpFormat = 'H:i:s';
                    } else {
                        $timerFormat = '%H:%M';
                        $phpFormat = 'H:i';
                    }

                    $str = \yii\helpers\Html::tag(
                        'span',
                        gmdate($phpFormat, $sec),
                        ['class' => 'badge badge-info timer',
                            'data-sec' => $sec,
                            'data-control' => 'start',
                            'data-format' => $timerFormat]
                    );
                }
            }
        }
        return $str;
    }

    public static function getDuration(?string $startDt, ?string $endDt): string
    {
        $str = '-';
        if ($startDt && $endDt) {
            $sec = strtotime($endDt) - strtotime($startDt);
            $diffHours =  round($sec / (60 * 60), 1);
            $str = Html::tag(
                'span',
                $diffHours . 'h',
                ['title' => \Yii::$app->formatter->asDuration($sec),
                    'class' => '']
            );
        }
        return $str;
    }
}

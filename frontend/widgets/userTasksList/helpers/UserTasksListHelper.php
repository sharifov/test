<?php

namespace frontend\widgets\userTasksList\helpers;

use src\auth\Auth;
use modules\taskList\src\entities\userTask\{
    UserTask,
    UserTaskHelper
};
use modules\taskList\abac\dto\TaskListAbacDto;
use modules\taskList\abac\TaskListAbacObject;
use yii\helpers\{
    StringHelper,
    Html
};
use yii\data\Pagination;

class UserTasksListHelper
{
    const PAGE_SIZE = 6;
    const CACHE_DURATION = 180;

    /**
     * @param int $leadId
     * @param int $userId
     * @param int $page
     * @param int $activeUserShiftScheduleId
     * @param string $prefix
     * @return string
     */
    public static function generateUserTasksListCacheKey(int $leadId, int $userId, int $page, int $activeUserShiftScheduleId): string
    {
        $cacheKey = md5('usertaskslist_' . $leadId . $userId . $page . $activeUserShiftScheduleId);
        return $cacheKey;
    }

    /**
     * @param int $leadId
     * @param int $userId
     * @return string
     */
    public static function getUserTasksListCacheTag(int $leadId, int $userId): string
    {
        $tag = md5('usertaskslisttag_' . $leadId . $userId);
        return $tag;
    }

    /**
     * @param int $leadId
     * @param int $userId
     * @return string
     */
    public static function generateUserSchedulesListCacheKey(int $leadId, int $userId): string
    {
        $cacheKey = 'userschedules_' . $leadId . $userId;
        return md5($cacheKey);
    }

    /**
     * Beautify deadline text
     *
     * @param $statusId
     * @param $startDate
     * @param $endDate
     * @return string
     */
    public static function renderDeadlineStatus($statusId, $startDate, $endDate, $timezone): string
    {
        if (static::isDeadline($endDate, $statusId, $timezone)) {
            return 'Unfulfilled';
        }

        if (!empty($endDate) && $statusId == UserTask::STATUS_PROCESSING) {
            $startDate = (new \DateTimeImmutable($startDate))->setTimezone(new \DateTimeZone($timezone))->format('d-M-Y H:i:s');
            $endDate = (new \DateTimeImmutable($endDate))->setTimezone(new \DateTimeZone($timezone))->format('d-M-Y H:i:s');

            $timer = UserTaskHelper::getDeadlineTimer($startDate, $endDate);
            $timer = ($timer != '-') ? $timer : '';

            return $timer;
        } elseif ($statusId == UserTask::STATUS_CANCEL) {
            return 'Canceled';
        }

        return '';
    }

    /**
     * @param $statusId
     * @param $completedTime
     * @param $timeZone
     * @return string
     */
    public static function renderCompletedStatus($statusId, $completedTime, $timeZone)
    {
        if (!empty($completedTime) && (int)$statusId === UserTask::STATUS_COMPLETE) {
            return \Yii::$app->formatter->asDateTimeByUserTimezone(
                strtotime($completedTime),
                $timeZone,
                'php:d.m.y [H:i]'
            );
        }

        return '';
    }

    /**
     * @param $userId
     * @param $usertaskId
     * @param $description
     * @return string
     */
    public static function renderNote($userId, $usertaskId, $description): string
    {
        $result = '';
        $dto = new TaskListAbacDto();
        $dto->setIsUserTaskOwner((int)$userId === Auth::id());

        /** @abac TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_ADD_NOTE, Access to add UserTask Note */
        if (\Yii::$app->abac->can($dto, TaskListAbacObject::OBJ_USER_TASK, TaskListAbacObject::ACTION_ADD_NOTE)) {
            $text = $description ? 'View note' : 'Add note';
            $activeClass = !empty($description) ? ' active ' : '';
            $classes = 'js-add_note_task_list lead-user-tasks-table__note-link js-tooltip ' . $activeClass;

            $result = Html::a($text, 'javascript:void(0)', [
                'class' => $classes,
                'title' => $description,
                'data' => [
                    'usertaskid' => $usertaskId,
                    'new-note' => empty($description),
                    'custom-class' => 'lead-user-tasks-table__note-tooltip',
                ],
            ]);
        } elseif (!empty($description)) {
            $result = Html::tag('span', StringHelper::truncate($description, 10), [
                'class' => 'js-tooltip',
                'data' => [
                    'custom-class' => 'lead-user-tasks-table__note-tooltip',
                    'original-title' => $description,
                ]
            ]);
        }

        return $result;
    }

    public static function renderStatusIcon($statusId, $isDeadline)
    {
        if ($isDeadline) {
            return '<i class="fa fa-times-circle fa-status-deadline" aria-hidden="true"></i>';
        }

        return UserTaskHelper::renderStatus($statusId);
    }

    public static function getColorByStatusAndDeadline($statucId, $isDeadline)
    {
        if ($isDeadline) {
            return 'rgba(225, 85, 84, .1)';
        }

        return UserTaskHelper::getColorByStatus($statucId);
    }

    public static function renderStartDate($statusId, $startDate, $isDeadline, $userTimeZone)
    {
        $result = '';

        if ($statusId != UserTask::STATUS_CANCEL && !$isDeadline) {
            $result = \Yii::$app->formatter->asDateTimeByUserTimezone(
                strtotime($startDate),
                $userTimeZone,
                'php:d.m.y [H:i]'
            );
        }

        return $result;
    }

    /**
     * @param $endDate
     * @param $timezone
     * @return bool
     * @throws \Exception
     */
    public static function isDeadline($endDate, $statusId, $timezone): bool
    {
        $deadline = false;

        if ($endDate && $statusId == UserTask::STATUS_PROCESSING) {
            $date = (new \DateTimeImmutable($endDate))->setTimezone(new \DateTimeZone($timezone))->format('d-M-Y H:i:s');

            if (time() > strtotime($date)) {
                $deadline = true;
            }
        }

        return $deadline;
    }

    /**
     * @param Pagination $pagination
     * @return array
     */
    public static function calcPagination(Pagination $pagination): array
    {
        $to = $pagination->getPageSize() * ($pagination->getPage() + 1);
        $to = ($to > $pagination->totalCount) ? $pagination->totalCount : $to;
        $from = ($pagination->getPageSize() * ($pagination->getPage() + 1) - $pagination->getPageSize() + 1);

        $result = [
            'to' => $to,
            'from' => $from,
            'totalCount' => $pagination->totalCount,
        ];

        return $result;
    }
}

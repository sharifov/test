<?php

namespace console\controllers;

use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use src\helpers\app\{
    AppHelper,
    DBHelper
};
use yii\console\Controller;
use modules\taskList\src\entities\userTask\UserTask;
use yii\helpers\Console;

class UserTaskController extends Controller
{
    /**
     * Set 'Failed' status for deadlined user tasks
     *
     * @return void
     */
    public function actionSetFailedStatusesForDeadlines()
    {
        $this->printInfo('Start....', $this->action->id, Console::BG_GREEN);

        try {
            $userTasks = UserTask::find()
                ->andWhere(['ut_status_id' => UserTask::STATUS_PROCESSING])
                ->andWhere(['=', 'ut_year', date('Y')])
                ->andWhere(['in', 'ut_month', [
                    date('n'),
                    date('n', strtotime(date('Y-n') . ' -1 month')),
                ]])
                ->andWhere(['<=', 'ut_end_dt', date('Y-m-d H:i')])
                ->all();

            array_reduce($userTasks, function ($carry, $userTask) {
                /** @var UserTask $userTask */
                $userTask->setStatusFailed();

                $userTaskRepository = new UserTaskRepository($userTask);
                $userTaskRepository->save();
            });
        } catch (\Throwable $e) {
            $this->printInfo($e->getMessage(), $this->action->id, Console::BG_RED);
            \Yii::error(AppHelper::throwableLog($e), 'UserTaskController:actionSetFailedStatusesForDeadlines:Throwable');
        }

        $this->printInfo('Statuses of deadlines tasks changed to "Failed"', $this->action->id, Console::BG_GREEN);
    }

    /**
     * Set 'Failed' status for ABSOLUTE ALL deadlined user tasks.
     *
     * @return void
     */
    public function actionSetFailedStatusesForAbsoluteAllDeadlines()
    {
        $this->printInfo('Start....', $this->action->id, Console::BG_GREEN);

        try {
            $dTStart = (new \DateTimeImmutable('2000-01-01 00:00:00'));
            $dTEnd = new \DateTime(date('Y-m-d H:i'));

            $userTasks = UserTask::find()
                ->andWhere(['ut_status_id' => UserTask::STATUS_PROCESSING])
                ->andWhere(DBHelper::yearMonthRestrictionQuery(
                    $dTStart,
                    $dTEnd,
                    'ut_year',
                    'ut_month'
                ))
                ->andWhere(['<=', 'ut_end_dt', date('Y-m-d H:i')])
                ->all();

            array_reduce($userTasks, function ($carry, $userTask) {
                /** @var UserTask $userTask */
                $userTask->setStatusFailed();

                $userTaskRepository = new UserTaskRepository($userTask);
                $userTaskRepository->save();
            });
        } catch (\Throwable $e) {
            $this->printInfo($e->getMessage(), $this->action->id, Console::BG_RED);
            \Yii::error(AppHelper::throwableLog($e), 'UserTaskController:actionSetFailedStatusForAbsoluteAllDeadlines:Throwable');
        }

        $this->printInfo('Statuses of deadlines tasks changed to "Failed"', $this->action->id, Console::BG_GREEN);
    }

    /**
     * @param string $info
     * @param string $action
     * @param int $color
     */
    private function printInfo(string $info, string $action = '', $color = Console::FG_YELLOW)
    {
        printf("\n --- %s %s ---\n", $info, $this->ansiFormat(self::class . '/' . $action, $color));
    }
}

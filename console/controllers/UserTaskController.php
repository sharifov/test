<?php

namespace console\controllers;

use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use src\helpers\app\DBHelper;
use yii\console\Controller;
use modules\taskList\src\entities\userTask\UserTask;
use yii\helpers\Console;
use yii\helpers\VarDumper;

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

        $startDateTime = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i') . ' -1 month'));
        $dTStart = (new \DateTimeImmutable($startDateTime))->setTime(0, 0);
        $dTEnd = (new \DateTime(date('Y-m-d H:i')));

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

        $this->saveTasks($userTasks, 'actionSetFailedStatusesForDeadlines');
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

        $userTasks = UserTask::find()
            ->andWhere(['ut_status_id' => UserTask::STATUS_PROCESSING])
            ->andWhere(['<=', 'ut_end_dt', date('Y-m-d H:i')])
            ->all();

        $this->saveTasks($userTasks, 'actionSetFailedStatusForAbsoluteAllDeadlines');
        $this->printInfo('Statuses of deadlines tasks changed to "Failed"', $this->action->id, Console::BG_GREEN);
    }

    private function saveTasks($userTasks, string $methodName)
    {
        if (!empty($userTasks)) {
            foreach ($userTasks as $userTask) {
                try {
                    /** @var UserTask $userTask */
                    $userTask->setStatusFailed();

                    $userTaskRepository = new UserTaskRepository($userTask);
                    $userTaskRepository->save();
                } catch (\Throwable $e) {
                    $message = [
                        'message' => $e->getMessage(),
                        'throwable' => $e,
                        'entity' => $userTask,
                    ];

                    $this->printInfo($e->getMessage(), $this->action->id, Console::BG_RED);
                    \Yii::error(VarDumper::dumpAsString($message), 'UserTaskController:' . $methodName . ':Throwable');
                }
            }
        }
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

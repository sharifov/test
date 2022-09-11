<?php

namespace console\controllers;

use src\helpers\app\AppHelper;
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
                ->andWhere(['<=', 'ut_end_dt', date('Y-m-d H:i')])
                ->all();

            $transaction = \Yii::$app->db->beginTransaction();
            array_reduce($userTasks, function ($carry, $userTask) {
                /** @var UserTask $userTask */
                $userTask->setStatusFailed()
                    ->update();
            });
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();

            $this->printInfo($e->getMessage(), $this->action->id, Console::BG_RED);
            \Yii::error(AppHelper::throwableLog($e), 'UserTaskController:actionSetFailedStatusesForDeadlines:Throwable');
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

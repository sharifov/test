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
            $db = \Yii::$app->db;
            $condition = 'ut_status_id != ' . UserTask::STATUS_FAILED . ' AND ut_end_dt <= ' . $db->quoteValue(date('Y-m-d H:i'));

            $isSuccess = (bool)$db->createCommand()->update(UserTask::tableName(), [
                'ut_status_id' => UserTask::STATUS_FAILED,
            ], $condition)->execute();

            if (!$isSuccess) {
                throw new \Exception('Failed to change status to `' . UserTask::STATUS_LIST[UserTask::STATUS_FAILED] . '` for deadline user tasks');
            }
        } catch (\Throwable $e) {
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

<?php

namespace console\controllers;

use common\models\UserConnection;
use common\models\UserOnline;
use sales\model\user\entity\monitor\UserMonitor;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Class UserController
 * @package console\controllers
 *
 */
class UserController extends Controller
{
    /**
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionUpdateOnlineStatus(): void
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $timeStart = microtime(true);
        $subQuery = UserConnection::find()->select(['DISTINCT(uc_user_id)']);
        $userOnlineForDelete = UserOnline::find()->where(['NOT IN', 'uo_user_id', $subQuery])->all();
        if ($userOnlineForDelete) {
            foreach ($userOnlineForDelete as $item) {
                echo ' - ' . $item->uo_user_id . PHP_EOL;
                $item->delete();
            }
        }

        $userOnline = UserOnline::find()->all();
        if ($userOnline) {
            foreach ($userOnline as $item) {
                $isUserIdle = UserMonitor::isUserIdle($item->uo_user_id);
                if ((bool) $item->uo_idle_state !== $isUserIdle) {
                    $item->uo_idle_state = $isUserIdle;
                    $item->uo_idle_state_dt = date('Y-m-d H:i:s');
                    if (!$item->update()) {
                        \Yii::error($item->errors, 'UserController:actionUpdateOnlineStatus:UserOnline:update');
                    }
                    echo ' - ' . $item->uo_user_id . ' : ' . ($isUserIdle ? 'idle' : 'active') . PHP_EOL;
                }
            }
        }

        $timeEnd = number_format(round(microtime(true) - $timeStart, 2), 2);
        $resultInfo = ' -- Execute Time: ' . $timeEnd;
        echo $resultInfo;
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}

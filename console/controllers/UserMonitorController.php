<?php

namespace console\controllers;

use common\models\Airline;
use common\models\ClientPhone;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Email;
use common\models\Employee;
use common\models\GlobalLog;
use common\models\Lead;
use common\models\LeadFlow;
//use common\models\LeadLog;
use common\models\LeadQcall;
use common\models\Notifications;
use common\models\Project;
use common\models\ProjectWeight;
use common\models\Quote;
use common\models\UserConnection;
use common\models\UserOnline;
use common\models\UserProjectParams;
use modules\requestControl\models\UserSiteActivity;
use src\entities\cases\Cases;
use src\helpers\email\TextConvertingHelper;
use src\logger\db\GlobalLogInterface;
use src\logger\db\LogDTO;
use src\model\user\entity\monitor\UserMonitor;
use src\services\lead\qcall\CalculateDateService;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Class UserMonitorController
 * @package console\controllers
 *
 */
class UserMonitorController extends Controller
{
    // todo may be deprecated. questions
    public function actionLogout2()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $leads = Lead::find()
            ->andWhere(['status' => 1])
            ->andWhere(['NOT IN', 'id', (new Query())->select('lqc_lead_id')->from(LeadQcall::tableName())])
            ->all();
        foreach ($leads as $lead) {
            $lq = new LeadQcall();
            $lq->lqc_lead_id = $lead->id;
            $lq->lqc_weight = 0;
            $lq->lqc_created_dt = $lead->created;

            $lq->lqc_dt_from = date('Y-m-d H:i:s');
            $lq->lqc_dt_to = date('Y-m-d H:i:s', strtotime('+3 days'));

            if (!$lq->save()) {
                Yii::error(VarDumper::dumpAsString($lq->errors), 'Lead:createOrUpdateQCall:LeadQcall:save');
            }
        }
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }

    /**
     * @throws \yii\db\Exception
     */
    public function actionConvertCollate()
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        $db = Yii::$app->getDb();
        // get the db name
        $schema = $db->createCommand('select database()')->queryScalar();
        // get all tables
        $tables = $db->createCommand('SELECT table_name FROM information_schema.tables WHERE table_schema=:schema AND table_type = "BASE TABLE"', [
            ':schema' => $schema
        ])->queryAll();
        $db->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();

        //VarDumper::dump($tables); exit;

        // Alter the encoding of each table
        foreach ($tables as $id => $table) {
            if (isset($table['table_name'])) {
                $tableName = $table['table_name'];
                $db->createCommand("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci")->execute();
                echo $id . " - tbl: " . $tableName . "\r\n";
            }

            if (isset($table['TABLE_NAME'])) {
                $tableName = $table['TABLE_NAME'];
                $db->createCommand("ALTER TABLE `$tableName` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci")->execute();
                echo $id . " - tbl: " . $tableName . "\r\n";
            }
        }
        $db->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
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


    /**
     * @param int|null $idleTimeMinutes
     */
    public function actionLogout(?int $idleTimeMinutes = null): void
    {
        $this->printInfo('Start', $this->action->id);
        $resultInfo = '';

        if (UserMonitor::isAutologoutEnabled()) {
            if ($idleTimeMinutes === null) {
                $idleTimeMinutes = UserMonitor::autologoutIdlePeriodMin();
            }

            $timerSec = UserMonitor::autoLogoutTimerSec();
            $isShowMessage = UserMonitor::isAutologoutShowMessage() ? 'true' : 'false';
            $timeStart = microtime(true);
            $users = UserOnline::find()
                ->where(['uo_idle_state' => true])
                ->andWhere(['<=', 'uo_idle_state_dt', date('Y-m-d H:i:s', strtotime('-' . $idleTimeMinutes . ' minutes'))])
                ->all();

            if ($users) {
                /** @var UserOnline $userOnline */
                foreach ($users as $userOnline) {
                    $pubChannel = UserConnection::getUserChannel($userOnline->uo_user_id);
                    if (Notifications::pub([$pubChannel], 'logout', ['timerSec' => $timerSec, 'isShowMessage' => $isShowMessage])) {
                        echo ' - user ' . $userOnline->uo_user_id . PHP_EOL;
                    }
                }
            }

            $resultInfo = 'Execute Time: ' . number_format(round(microtime(true) - $timeStart, 2), 2);
        } else {
            echo 'Site Setting params isAutologoutEnabled = false' . PHP_EOL;
        }
        $this->printInfo('End ' . $resultInfo, $this->action->id);
    }
}

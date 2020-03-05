<?php

namespace console\controllers;

use common\models\Client;
use thamtech\uuid\helpers\UuidHelper;
use yii\console\Controller;
use yii\helpers\BaseConsole;
use yii\helpers\Console;
use yii\helpers\VarDumper;

class OneTimeController extends Controller
{
    public $limit;

    public function options($actionID)
    {
        if ($actionID === 'generate-client-uuid') {
            return array_merge(parent::options($actionID), [
                'limit'
            ]);
        }
        return parent::options($actionID);
    }

    public function actionGenerateClientUuid($limit): void
    {
        printf(PHP_EOL . '--- Start [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));

        printf(PHP_EOL);

//        $limit = BaseConsole::input('Enter Limit records: ');

        $limit = (int)$limit;

        $time_start = microtime(true);

        $logs = [];
        foreach (Client::find()->andWhere(['IS', 'uuid', null])->limit($limit)->orderBy(['id' => SORT_ASC])->all() as $client) {
            $client->uuid = UuidHelper::uuid();
            if (!$client->save()) {
                $logs[$client->id] = VarDumper::dumpAsString($client->getErrors());
            }
        }

        if ($logs) {
            echo 'Errors: ' . PHP_EOL;
            foreach ($logs as $key => $log) {
                echo  'Client Id: ' . $key . ' error: ' . $log  . PHP_EOL;
            }
            echo PHP_EOL;
        }

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        printf(PHP_EOL . 'Execute Time: %s' . PHP_EOL, $this->ansiFormat($time . ' s', Console::FG_RED));
        printf(PHP_EOL . ' --- End [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
    }

    public function actionDropLeadLogsTable(): void
    {
        printf(PHP_EOL . '--- Start [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
        $time_start = microtime(true);

        \Yii::$app->db->createCommand()->dropTable('{{%lead_logs}}')->execute();

        $time_end = microtime(true);
        $time = number_format(round($time_end - $time_start, 2), 2);

        printf(PHP_EOL . 'Execute Time: %s' . PHP_EOL, $this->ansiFormat($time . ' s', Console::FG_RED));
        printf(PHP_EOL . ' --- End [' . date('Y-m-d H:i:s') . '] %s ---' . PHP_EOL . PHP_EOL, $this->ansiFormat(self::class . '\\' . $this->action->id, Console::FG_YELLOW));
    }
}

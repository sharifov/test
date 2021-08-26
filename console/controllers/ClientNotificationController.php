<?php

namespace console\controllers;

use sales\helpers\app\AppHelper;
use sales\model\client\notifications\ClientNotificationExecutor;
use sales\model\client\notifications\phone\ClientNotificationPhoneExecutor;
use sales\model\client\notifications\phone\entity\ClientNotificationPhoneList;
use yii\console\Controller;
use yii\console\widgets\Table;
use yii\helpers\Console;

/**
 * Class ClientNotificationController
 *
 * @property ClientNotificationPhoneExecutor $clientNotificationPhoneExecutor
 */
class ClientNotificationController extends Controller
{
    private ClientNotificationPhoneExecutor $clientNotificationPhoneExecutor;

    public function __construct($id, $module, ClientNotificationPhoneExecutor $clientNotificationPhoneExecutor, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->clientNotificationPhoneExecutor = $clientNotificationPhoneExecutor;
    }

    public function actionNotify()
    {
        $this->callNotify();
    }

    private function callNotify(): void
    {
        print("Call Notify\n");

        $phoneNotifications = ClientNotificationPhoneList::find()
            ->new()
//            ->andWhere(['cnfl_start']) todo
//            ->andWhere(['cnfl_end']) todo
            ->limit(100)
            ->all();

        $count = count($phoneNotifications);

        print("Found notifications " . $count . "\n");

        $n = 0;

        Console::startProgress(0, $count, 'Process: ', false);

        $rows = [];
        foreach ($phoneNotifications as $phoneNotification) {
            try {
                $this->clientNotificationPhoneExecutor->execute($phoneNotification);
                $rows[] = [
                    $phoneNotification->cnfl_id,
                    'done',
                    'success'
                ];
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Client Phone Notification processing error',
                    'phoneNotificationId' => $phoneNotification->cnfl_id,
                    'exception' => AppHelper::throwableLog($e),
                ], 'ClientNotificationController:callNotify');
                $rows[] = [
                    $phoneNotification->cnfl_id,
                    sprintf("%s", $this->ansiFormat("error", Console::BG_RED)),
                    $e->getMessage(),
                ];
            }
            $n++;
            Console::clearLineBeforeCursor();
            Console::updateProgress($n, $count);
        }

        Console::endProgress("done." . PHP_EOL);

        echo Table::widget([
            'headers' => [
                'Id',
                'Result',
                'Message',
            ],
            'rows' => $rows,
        ]);
    }
}

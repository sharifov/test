<?php

namespace console\controllers;

use sales\helpers\app\AppHelper;
use sales\model\client\notifications\client\entity\ClientNotification;
use sales\model\client\notifications\client\entity\CommunicationType;
use sales\model\client\notifications\phone\ClientNotificationPhoneExecutor;
use sales\model\client\notifications\phone\entity\ClientNotificationPhoneList;
use sales\model\client\notifications\phone\entity\Status as PhoneStatus;
use sales\model\client\notifications\sms\entity\Status as SmsStatus;
use sales\model\client\notifications\sms\ClientNotificationSmsExecutor;
use sales\model\client\notifications\sms\entity\ClientNotificationSmsList;
use sales\services\TransactionManager;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;

/**
 * Class ClientNotificationController
 *
 * @property ClientNotificationPhoneExecutor $clientNotificationPhoneExecutor
 * @property ClientNotificationSmsExecutor $clientNotificationSmsExecutor
 * @property TransactionManager $transactionManager
 */
class ClientNotificationController extends Controller
{
    private ClientNotificationPhoneExecutor $clientNotificationPhoneExecutor;
    private ClientNotificationSmsExecutor $clientNotificationSmsExecutor;
    private TransactionManager $transactionManager;

    public function __construct(
        $id,
        $module,
        ClientNotificationPhoneExecutor $clientNotificationPhoneExecutor,
        ClientNotificationSmsExecutor $clientNotificationSmsExecutor,
        TransactionManager $transactionManager,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->clientNotificationPhoneExecutor = $clientNotificationPhoneExecutor;
        $this->clientNotificationSmsExecutor = $clientNotificationSmsExecutor;
        $this->transactionManager = $transactionManager;
    }

    public function actionClear()
    {
        $date = (new \DateTimeImmutable('-30 day'))->format('Y-m-d H:i:s');

        $smsNotifications = ClientNotificationSmsList::find()
            ->select(['cnsl_id'])
            ->andWhere(['<>', 'cnsl_status_id', SmsStatus::NEW])
            ->andWhere(['<', 'cnsl_created_dt', $date])
            ->column();
        foreach ($smsNotifications as $notificationId) {
            $this->transactionManager->wrap(function () use ($notificationId) {
                ClientNotificationSmsList::deleteAll(['cnsl_id' => $notificationId]);
                ClientNotification::deleteAll(['cn_communication_object_id' => $notificationId, 'cn_communication_type_id' => CommunicationType::SMS]);
            });
        }

        $phoneNotifications = ClientNotificationPhoneList::find()
            ->select(['cnfl_id'])
            ->andWhere(['<>', 'cnfl_status_id', PhoneStatus::NEW])
            ->andWhere(['<', 'cnfl_created_dt', $date])
            ->column();
        foreach ($phoneNotifications as $notificationId) {
            $this->transactionManager->wrap(function () use ($notificationId) {
                ClientNotificationPhoneList::deleteAll(['cnfl_id' => $notificationId]);
                ClientNotification::deleteAll(['cn_communication_object_id' => $notificationId, 'cn_communication_type_id' => CommunicationType::PHONE]);
            });
        }

        return ExitCode::OK;
    }

    public function actionNotify()
    {
        $this->callNotify();
        $this->smsNotify();
        return ExitCode::OK;
    }

    private function callNotify(): void
    {
        print("Call Notify\n");

        $notifications = ClientNotificationPhoneList::find()
            ->new()
//            ->andWhere(['cnfl_start']) todo
//            ->andWhere(['cnfl_end']) todo
            ->limit(100)
            ->all();

        $count = count($notifications);

        print("Found notifications " . $count . "\n");

        $n = 0;

        Console::startProgress(0, $count, 'Process: ', false);

        $rows = [];
        foreach ($notifications as $notification) {
            try {
                $this->clientNotificationPhoneExecutor->execute($notification);
                $rows[] = [
                    $notification->cnfl_id,
                    'done',
                    'success'
                ];
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Client Phone Notification processing error',
                    'phoneNotificationId' => $notification->cnfl_id,
                    'exception' => AppHelper::throwableLog($e),
                ], 'ClientNotificationController:callNotify');
                $rows[] = [
                    $notification->cnfl_id,
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

    private function smsNotify(): void
    {
        print("Sms Notify\n");

        $notifications = ClientNotificationSmsList::find()
            ->new()
//            ->andWhere(['cnfl_start']) todo
//            ->andWhere(['cnfl_end']) todo
            ->limit(100)
            ->all();

        $count = count($notifications);

        print("Found notifications " . $count . "\n");

        $n = 0;

        Console::startProgress(0, $count, 'Process: ', false);

        $rows = [];
        foreach ($notifications as $notification) {
            try {
                $this->clientNotificationSmsExecutor->execute($notification);
                $rows[] = [
                    $notification->cnsl_id,
                    'done',
                    'success'
                ];
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Client Sms Notification processing error',
                    'phoneNotificationId' => $notification->cnsl_id,
                    'exception' => AppHelper::throwableLog($e),
                ], 'ClientNotificationController:smsNotify');
                $rows[] = [
                    $notification->cnsl_id,
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

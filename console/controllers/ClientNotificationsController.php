<?php

namespace console\controllers;

use src\helpers\app\AppHelper;
use src\model\client\notifications\email\ClientNotificationEmailExecutor;
use src\model\client\notifications\email\entity\ClientNotificationEmailList;
use src\model\client\notifications\phone\ClientNotificationPhoneExecutor;
use src\model\client\notifications\phone\entity\ClientNotificationPhoneList;
use src\model\client\notifications\sms\ClientNotificationSmsExecutor;
use src\model\client\notifications\sms\entity\ClientNotificationSmsList;
use src\services\TransactionManager;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;

/**
 * Class ClientNotificationController
 *
 * @property ClientNotificationPhoneExecutor $clientNotificationPhoneExecutor
 * @property ClientNotificationSmsExecutor $clientNotificationSmsExecutor
 * @property ClientNotificationEmailExecutor $clientNotificationEmailExecutor
 * @property TransactionManager $transactionManager
 */
class ClientNotificationsController extends Controller
{
    private ClientNotificationPhoneExecutor $clientNotificationPhoneExecutor;
    private ClientNotificationSmsExecutor $clientNotificationSmsExecutor;
    private ClientNotificationEmailExecutor $clientNotificationEmailExecutor;
    private TransactionManager $transactionManager;

    public function __construct(
        $id,
        $module,
        ClientNotificationPhoneExecutor $clientNotificationPhoneExecutor,
        ClientNotificationSmsExecutor $clientNotificationSmsExecutor,
        ClientNotificationEmailExecutor $clientNotificationEmailExecutor,
        TransactionManager $transactionManager,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->clientNotificationPhoneExecutor = $clientNotificationPhoneExecutor;
        $this->clientNotificationSmsExecutor = $clientNotificationSmsExecutor;
        $this->clientNotificationEmailExecutor = $clientNotificationEmailExecutor;
        $this->transactionManager = $transactionManager;
    }

    public function actionNotify()
    {
        $this->callNotify();
        $this->smsNotify();
        $this->emailNotify();
        return ExitCode::OK;
    }

    private function callNotify(): void
    {
        print("Call Notify\n");

        $now = new \DateTimeImmutable();
        $nowDate = $now->format('Y-m-d H:i:s');
        $nowHour = (int)$now->format('H');

        $notifications = ClientNotificationPhoneList::find()
            ->new()
            ->andWhere(['<=', 'cnfl_start', $nowDate])
            ->andWhere(['>=', 'cnfl_end', $nowDate])
            ->andWhere([
                'OR',
                [
                    'AND',
                    'cnfl_from_hours > cnfl_to_hours',
                    [
                        'OR',
                        ['<=', 'cnfl_from_hours', $nowHour],
                        ['>', 'cnfl_to_hours', $nowHour],
                    ],
                ],
                [
                    'AND',
                    'cnfl_from_hours < cnfl_to_hours',
                    ['<=', 'cnfl_from_hours', $nowHour],
                    ['>', 'cnfl_to_hours', $nowHour],
                ],
            ])
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
                ], 'ClientNotificationsController:callNotify');
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

        $now = date('Y-m-d H:i:s');
        $notifications = ClientNotificationSmsList::find()
            ->new()
            ->andWhere([
                'OR',
                ['IS', 'cnsl_start', null],
                ['<=', 'cnsl_start', $now]
            ])
            ->andWhere([
                'OR',
                ['IS', 'cnsl_end', null],
                ['>=', 'cnsl_end', $now]
            ])
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
                ], 'ClientNotificationsController:smsNotify');
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

    private function emailNotify(): void
    {
        print("Email Notify\n");

        $now = date('Y-m-d H:i:s');
        $notifications = ClientNotificationEmailList::find()
            ->new()
            ->andWhere([
                'OR',
                ['IS', 'cnel_start', null],
                ['<=', 'cnel_start', $now]
            ])
            ->andWhere([
                'OR',
                ['IS', 'cnel_end', null],
                ['>=', 'cnel_end', $now]
            ])
            ->limit(100)
            ->all();

        $count = count($notifications);

        print("Found notifications " . $count . "\n");

        $n = 0;

        Console::startProgress(0, $count, 'Process: ', false);

        $rows = [];
        foreach ($notifications as $notification) {
            try {
                $this->clientNotificationEmailExecutor->execute($notification);
                $rows[] = [
                    $notification->cnel_id,
                    'done',
                    'success'
                ];
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Client Email Notification processing error',
                    'emailNotificationId' => $notification->cnel_id,
                    'exception' => AppHelper::throwableLog($e),
                ], 'ClientNotificationsController:emailNotify');
                $rows[] = [
                    $notification->cnel_id,
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

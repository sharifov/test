<?php

namespace console\controllers;

use common\components\debug\ConsoleTarget;
use common\components\debug\DbTarget;
use common\components\debug\Logger;
use common\components\debug\Message;
use modules\email\src\entity\emailAccount\EmailAccount;
use modules\email\src\Notifier;
use modules\email\src\protocol\gmail\GmailApiService;
use modules\email\src\protocol\gmail\GmailClient;
use modules\email\src\protocol\gmail\GmailCriteria;
use modules\email\src\protocol\gmail\GmailService;
use sales\services\email\EmailService;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionTest()
    {
        $emailAccountId = 1;
        $dayTo = null;
        $limit = 500;
        $dayFrom = null;
        $debugMode = true;

//        $logger = new Logger($debugMode, new DbTarget('info\GmailJob'));
        $logger = new Logger($debugMode, new ConsoleTarget());
        $logger->timerStart('gmail_job')->log(Message::info('Start Gmail Job ' . date('Y-m-d H:i:s')));

        if (!$account = EmailAccount::findOne($emailAccountId)) {
            $message = 'Not found EmailAccount Id: ' . $emailAccountId;
            $logger->log(Message::error($message));
            $logger->release();
            \Yii::error($message, 'GmailJob');
            return;
        }

        try {

            $logger->timerStart('gmail_service')->log(Message::info('Start Gmail Service'));

            $api = new GmailApiService(GmailClient::createByAccount($account), $account->ea_email, $logger, $useBatchRequest = true);
            $emailService = \Yii::createObject(EmailService::class);
            $result = (new GmailService($api, $account, $logger, $emailService))
                ->downloadMessages($account->ea_gmail_command, new GmailCriteria($dayTo, $limit, $dayFrom));

            if ($result->emailsTo) {
                $logger->timerStart('notify')->log(Message::info('Start Notify'));
                $notifier = new Notifier();
                $notifier->notify($result->emailsTo);
                $logger->timerStop('notify')->log(Message::info('Finish Notify'));
            }

            $logger->timerStop('gmail_service')->log(Message::info('End Gmail Service'));

        } catch (\Throwable $e) {
            \Yii::error($e, 'GmailJob');
            $logger->log(Message::error($e->getMessage()));
        }

        $logger->timerStop('gmail_job')->log(Message::info('Finish Gmail Job ' . date('Y-m-d H:i:s')));
        $logger->release();
    }
}

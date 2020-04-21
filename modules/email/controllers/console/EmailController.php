<?php

namespace modules\email\controllers\console;

use common\components\debug\ConsoleTarget;
use common\components\debug\DbTarget;
use common\components\debug\Logger;
use common\components\debug\Message;
use modules\email\src\entity\emailAccount\EmailAccount;
use modules\email\src\protocol\gmail\GmailJob;
use Yii;
use yii\console\Controller;
use yii\helpers\VarDumper;

class EmailController extends Controller
{
    public function actionDownload(?int $dayTo = null, int $limit = 500, ?int $dayFrom = null, bool $debugMode = true): void
    {
//        $logger = new Logger($debugMode, new ConsoleTarget());
        $logger = new Logger($debugMode, new DbTarget('info\DownloadMessages'));
        $logger->timerStart('email_download_index')->log(Message::start('--- Start: ' . date('Y-m-d H:i:s') . '  ' . (new \ReflectionClass(self::class))->getShortName() . ':' . __FUNCTION__ . ' ---'));

        $processed = $failed = 0;
        $accounts = EmailAccount::findAll(['ea_active' => true]);

        if ($accounts) {
            foreach ($accounts as $account) {
                try {

                    if ($account->isGmailApi()) {
                        $logger->log(Message::info('-- GMAIL API protocol start --'));
                        $job = new GmailJob([
                            'emailAccountId' => $account->ea_id,
                            'limit' => $limit,
                            'dayFrom' => $dayFrom,
                            'dayTo' => $dayTo,
                            'debugMode' => $debugMode,
                        ]);
                        $accountStr = 'Account Id: "' . $account->ea_id . '" Account email: "' . $account->ea_email . '"';
                        if ($pushRes = \Yii::$app->queue_gmail_download->push($job)) {
                            $logger->log(Message::success($accountStr . ' Created Gmail Job: "' . $pushRes . '"'));
                        } else {
                            $logger->log(Message::error($accountStr . ' Failed Gmail Job'));
                        }
                        $logger->log(Message::info('-- GMAIL API protocol finish --'));
                    } else {
//                        $logger->log(Message::info('-- IMAP protocol start --'));
//                        $result = (new ImapService($account, $logger, $dayTo, $limit, $dayFrom))->saveAccountMails(true, $projectList);
//                        if ($result->lastEmailId > 0) {
//                            (new MailJob($result->projectsIds, $result->lastEmailId, $logger))->createJob();
//                        }
//                        $logger->log(Message::info('-- IMAP protocol finish --'));
                    }

                    $processed++;
                } catch (\Throwable $throwable) {
                    $logger->log(Message::error(VarDumper::dumpAsString($throwable->getMessage(), 20)));
                    Yii::error(VarDumper::dumpAsString($throwable->getMessage(), 20), 'ImapController:actionIndex:precessing');
                    $failed++;
                }
            }

            $logger->log(Message::info('--- --- --- --- ---'));
            $logger->log(Message::success('Processed accounts: ' . $processed));

            if ($failed) {
                $logger->log(Message::error('Failed accounts: ' . $failed));
            }

        } else {
            $logger->log(Message::error('Not found active accounts'));
        }

        $logger->timerStop('email_download_index')->log(Message::finish('--- Stop: ' . date('Y-m-d H:i:s') . '  ' . (new \ReflectionClass(self::class))->getShortName() . ':' . __FUNCTION__ ));

        $logger->release();
    }
}

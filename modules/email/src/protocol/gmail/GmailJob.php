<?php

namespace modules\email\src\protocol\gmail;

use common\components\debug\DbTarget;
use common\components\debug\Logger;
use common\components\debug\Message;
use modules\email\src\entity\emailAccount\EmailAccount;
use modules\email\src\Notifier;
use sales\services\email\EmailService;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Class GmailJob
 * @property int $emailAccountId
 * @property array $projectList
 * @property int|null $dayTo
 * @property int $limit
 * @property int|null $dayFrom
 * @property bool $debugMode
 */
class GmailJob extends BaseObject implements JobInterface
{
    public $emailAccountId;
    public $projectList = [];
    public $dayTo;
    public $limit = 400;
    public $dayFrom;
    public $debugMode = false;

    public function execute($queue)
    {
        $logger = new Logger($this->debugMode, new DbTarget('info\GmailJob'));
        $logger->timerStart('gmail_job')->log(Message::info('Start Gmail Job ' . date('Y-m-d H:i:s')));

        if (!$account = EmailAccount::findOne($this->emailAccountId)) {
            $message = 'Not found EmailAccount Id: ' . $this->emailAccountId;
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
                ->downloadMessages($account->ea_gmail_command, new GmailCriteria($this->dayTo, $this->limit, $this->dayFrom));

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

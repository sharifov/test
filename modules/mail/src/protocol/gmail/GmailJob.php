<?php

namespace modules\mail\src\gmail;

use common\components\debug\DbTarget;
use common\components\debug\Logger;
use common\components\debug\Message;
use common\components\mail\MailJob;
use common\models\EmailAccount;
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
            $result = (new GmailService($api, $account, $this->projectList, $logger))
                ->downloadMessages($account->ea_gmail_api_command, new GmailCriteria($this->dayTo, $this->limit, $this->dayFrom));
            $logger->timerStop('gmail_service')->log(Message::info('End Gmail Service'));

            if ($result->lastEmailId > 0) {
                (new MailJob($result->projectsIds, $result->lastEmailId, $logger))->createJob();
            }
        } catch (\Throwable $e) {
            \Yii::error($e, 'GmailJob');
            $logger->log(Message::error($e->getMessage()));
        }

        $logger->timerStop('gmail_job')->log(Message::info('Finish Gmail Job ' . date('Y-m-d H:i:s')));
        $logger->release();
    }
}

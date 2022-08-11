<?php

namespace modules\email\src\protocol\gmail;

use common\components\debug\DbTarget;
use common\components\debug\Logger;
use common\components\debug\Message;
use modules\email\src\entity\emailAccount\EmailAccount;
use modules\email\src\Notifier;
use modules\email\src\Projects;
use src\helpers\app\AppHelper;
use src\services\email\EmailMainService;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Class GmailJob
 * @property array $projects
 * @property int $emailAccountId
 * @property int|null $dayTo
 * @property int $limit
 * @property int|null $dayFrom
 * @property bool $debugMode
 * @property bool $useBatchRequest
 */
class GmailJob extends BaseObject implements JobInterface
{
    public $projects = [];
    public $emailAccountId;
    public $dayTo;
    public $limit = 400;
    public $dayFrom;
    public $debugMode = false;
    public $useBatchRequest = true;

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

            $api = new GmailApiService(GmailClient::createByAccount($account), $account->ea_email, $logger, $this->useBatchRequest);
            $result = (new GmailService($api, $account, $logger, EmailMainService::newInstance(), new Projects($this->projects)))
                ->downloadMessages($account->ea_gmail_command, new GmailCriteria($this->dayTo, $this->limit, $this->dayFrom));

            if ($result->emailsTo) {
                $logger->timerStart('notify')->log(Message::info('Start Notify'));
                $notifier = new Notifier();
                $notifier->notifyToEmails($result->emailsTo);
                $logger->timerStop('notify')->log(Message::info('Finish Notify'));
            }

            $logger->timerStop('gmail_service')->log(Message::info('End Gmail Service'));
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableFormatter($e), 'GmailJob');
            $logger->log(Message::error($e->getMessage()));
        }

        $logger->timerStop('gmail_job')->log(Message::info('Finish Gmail Job ' . date('Y-m-d H:i:s')));
        $logger->release();
    }
}

<?php

namespace common\components\jobs;

use src\helpers\app\AppHelper;
use src\repositories\NotFoundException;
use src\services\email\SendEmailByCase;
use yii\queue\JobInterface;

/**
 * Class SendEmailOnCaseCreationBOJob
 * @package common\components\jobs
 *
 * @property int $case_id
 * @property string $contact_email
 */
class SendEmailOnCaseCreationBOJob extends BaseJob implements JobInterface
{
    public $case_id;
    public $contact_email;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        try {
            (new SendEmailByCase($this->case_id, $this->contact_email));
        } catch (NotFoundException | \RuntimeException | \DomainException $e) {
            $message = AppHelper::throwableLog($e);
            $message['case_id'] = $this->case_id;
            \Yii::warning($message, 'SendEmailOnCaseCreationBOJob::Exception');
        } catch (\Throwable $e) {
            $message = AppHelper::throwableLog($e);
            $message['case_id'] = $this->case_id;
            \Yii::error($message, 'SendEmailOnCaseCreationBOJob::Throwable');
        }
    }
}

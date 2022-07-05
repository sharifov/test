<?php

namespace common\components\jobs;

use common\components\email\dto\EmailDto;
use src\helpers\app\AppHelper;
use yii\queue\JobInterface;

class SendInternalEmailJob extends BaseJob implements JobInterface
{
    private $emailFrom;
    private $emailTo;
    private $emailSubject;
    private $emailTemplate;

    public function __construct(
        string $emailFrom,
        string $emailTo,
        string $emailTemplate,
        string $emailSubject,
        ?float $timeStart = null,
        $config = []
    ) {
        parent::__construct($timeStart, $config);
        $this->emailFrom = $emailFrom;
        $this->emailTo = $emailTo;
        $this->emailTemplate = $emailTemplate;
        $this->emailSubject = $emailSubject;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();

        try {
            $emailDto = new EmailDto(
                $this->emailTo,
                $this->emailFrom,
                $this->emailSubject,
                $this->emailTemplate
            );

            \Yii::$app->email->send($emailDto);
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e, true), 'SendInternalEmailJob');
        }
    }
}

<?php

namespace common\components\jobs;

use common\components\email\dto\EmailDto;
use src\helpers\app\AppHelper;
use yii\queue\JobInterface;

class SendInternalEmailJob extends BaseJob implements JobInterface
{
    private $projectId;
    private $templateType;
    private $emailFrom;
    private $emailTo;
    private $emailData;

    public function __construct(
        int $projectId,
        string $templateType,
        string $emailFrom,
        string $emailTo,
        array $emailData,
        ?float $timeStart = null,
        $config = []
    ) {
        parent::__construct($timeStart, $config);
        $this->projectId = $projectId;
        $this->templateType = $templateType;
        $this->emailFrom = $emailFrom;
        $this->emailTo = $emailTo;
        $this->emailData = $emailData;
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();

        $emailData = \Yii::$app->communication->mailPreview(
            $this->projectId,
            $this->templateType,
            $this->emailFrom,
            $this->emailTo,
            $this->emailData
        );

        try {
            if ($emailData['error'] !== false) {
                throw new \DomainException($emailData['error']);
            }

            $emailDto = new EmailDto(
                $this->emailTo,
                $this->emailFrom,
                $emailData['data']['email_subject'],
                $emailData['data']['email_body_html']
            );

            $sendResult = \Yii::$app->email->send($emailDto);
            \Yii::info($emailData, 'info\emaildata');
            \Yii::info(['send' => $sendResult], 'info\emaildata');
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e, true), 'SendInternalEmailJob');
        }
    }
}

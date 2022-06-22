<?php

namespace common\components\jobs;

use common\models\Lead;
use src\helpers\app\AppHelper;
use src\services\quote\addQuote\AddQuoteService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

class AutoAddQuoteJob extends BaseJob implements JobInterface
{
    public int $leadId;

    public function __construct(int $leadId, ?float $timeStart = null, array $config = [])
    {
        $this->leadId = $leadId;
        parent::__construct($timeStart, $config);
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        try {
            $lead = $this->findLead();
            $autoQuoteService = \Yii::createObject(AddQuoteService::class);
            $autoQuoteService->addAutoQuotes($lead);
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['lead_id' => $this->leadId]);
            Yii::warning($message, 'AutoAddQuoteJob::execute::Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable, true), ['lead_id' => $this->leadId]);
            Yii::warning($message, 'AutoAddQuoteJob::execute::Throwable');
        }
    }

    private function findLead(): Lead
    {
        $lead = Lead::findOne($this->leadId);
        if (!$lead) {
            throw new \RuntimeException('Lead not found');
        }
        return $lead;
    }
}

<?php

namespace src\model\voip\overridePhoneTo;

use src\helpers\setting\SettingHelper;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class OverrideJob extends BaseObject implements JobInterface
{
    public $fromDate;
    public $toDate;
    public $endDate;
    public $stepHours;
    public $phones;

    public function execute($queue)
    {
        if (!SettingHelper::isSyncOverridePhoneToEnable()) {
            \Yii::info([
                'message' => 'Override job ended. Setting Sync is disabled',
                'fromDate' => $this->fromDate,
                'toDate' => $this->toDate,
                'endDate' => $this->endDate,
                'phones' => $this->phones,
                'stepHours' => $this->stepHours,
            ], 'info\OverridePhoneToService');
            return;
        }
        try {
            $service = \Yii::createObject(OverrideService::class);
            $from = new \DateTimeImmutable($this->fromDate);
            $to = new \DateTimeImmutable($this->toDate);
            $report = $service->override(
                $from,
                $to,
                $this->phones,
            );
            \Yii::info([
                'message' => 'Sync with params is completed',
                'params' => [
                    'fromDate' => $this->fromDate,
                    'toDate' => $this->toDate,
                    'endDate' => $this->endDate,
                    'phones' => $this->phones,
                    'stepHours' => $this->stepHours,
                ],
                'report' => $report,
            ], 'info\OverridePhoneToService');
            $nextDates = new NextDates($to, new \DateTimeImmutable($this->endDate), $this->stepHours);
            if ($nextDates->isExpired()) {
                \Yii::info([
                    'message' => 'All dates completed',
                    'params' => [
                        'fromDate' => $nextDates->getFromDate()->format('Y-m-d H:i:s'),
                        'toDate' => $nextDates->getToDate()->format('Y-m-d H:i:s'),
                        'endDate' => $nextDates->getEndDate()->format('Y-m-d H:i:s'),
                        'stepHours' => $nextDates->getStepHours(),
                        'phones' => $this->phones,
                    ],
                ], 'info\OverridePhoneToService');
                return;
            }
            \Yii::$app->queue_job->push(
                new self([
                    'fromDate' => $nextDates->getFromDate()->format('Y-m-d H:i:s'),
                    'toDate' => $nextDates->getToDate()->format('Y-m-d H:i:s'),
                    'endDate' => $nextDates->getEndDate()->format('Y-m-d H:i:s'),
                    'stepHours' => $nextDates->getStepHours(),
                    'phones' => $this->phones,
                ])
            );
        } catch (\Throwable $e) {
            \Yii::error([
                'error' => $e->getMessage(),
                'message' => 'Processing override service error',
                'params' => [
                    'fromDate' => $this->fromDate,
                    'toDate' => $this->toDate,
                    'endDate' => $this->endDate,
                    'phones' => $this->phones,
                    'stepHours' => $this->stepHours,
                ],
            ], 'OverridePhoneToService');
        }
    }
}

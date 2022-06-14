<?php

namespace console\controllers;

use src\model\voip\overridePhoneTo\NextDates;
use src\model\voip\overridePhoneTo\OverrideJob;
use yii\console\Controller;
use Yii;
use yii\helpers\Console;
use yii\validators\DateValidator;
use yii\validators\StringValidator;

class CallOverrideController extends Controller
{
    public $fromDate;
    public $toDate;
    public $stepHours;
    public $phones;

    public function options($actionID)
    {
        if ($actionID === 'override-phone-to') {
            return array_merge(parent::options($actionID), [
                'fromDate', 'toDate', 'endDate', 'stepHours', 'phones'
            ]);
        }
        return parent::options($actionID);
    }

    public function actionOverridePhoneTo(): void
    {
        echo Console::renderColoredString('%g --- Start %w[' . date('Y-m-d H:i:s') . '] %g' .
            self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
        $timeStart = microtime(true);

        $dateValidator = new DateValidator([
            'skipOnEmpty' => false,
            'format' => 'php:Y-m-d H'
        ]);

        if (!$dateValidator->validate($this->fromDate, $errorFromDate)) {
            Yii::error([
                'param' => 'fromDate',
                'error' => $errorFromDate,
            ], 'CallOverrideController:actionOverridePhoneTo');
        }

        if (!$dateValidator->validate($this->toDate, $errorToDate)) {
            Yii::error([
                'param' => 'toDate',
                'error' => $errorToDate,
            ], 'CallOverrideController:actionOverridePhoneTo');
        }

        $stepHoursValidator = new  \yii\validators\NumberValidator([
            'integerOnly' => true,
            'skipOnEmpty' => false,
            'min' => 1,
            'max' => 240,
        ]);
        if (!$stepHoursValidator->validate($this->stepHours, $errorStepHours)) {
            Yii::error([
                'param' => 'stepHours',
                'error' => $errorStepHours,
            ], 'CallOverrideController:actionOverridePhoneTo');
        }

        $phonesValidator = new StringValidator([
            'skipOnEmpty' => false,
        ]);
        if (!$phonesValidator->validate($this->phones, $errorPhones)) {
            Yii::error([
                'param' => 'phones',
                'error' => $errorPhones,
            ], 'CallOverrideController:actionOverridePhoneTo');
        }
        $phones = explode(',', $this->phones);
        if (!$phones) {
            $errorPhones = 'Not found phones';
            Yii::error([
                'param' => 'phones',
                'error' => $errorPhones,
            ], 'CallOverrideController:actionOverridePhoneTo');
        }

        if (!$errorFromDate && !$errorToDate && !$errorStepHours && !$errorPhones) {
            $fromDate = new \DateTimeImmutable($this->fromDate . ':00:00');
            $toDate = new \DateTimeImmutable($this->toDate . ':00:00');
            $stepHours = (int)$this->stepHours;

            $nextDates = new NextDates($fromDate, $toDate, $stepHours);
            if ($nextDates->isExpired()) {
                Yii::error([
                    'message' => 'ToDate must be more then FromDate',
                    'params' => [
                        'fromDate' => $nextDates->getFromDate()->format('Y-m-d H:i:s'),
                        'toDate' => $nextDates->getToDate()->format('Y-m-d H:i:s'),
                        'endDate' => $nextDates->getEndDate()->format('Y-m-d H:i:s'),
                    ]
                ], 'CallOverrideController:actionOverridePhoneTo');
            } else {
                $jobId = \Yii::$app->queue_job->push(
                    new OverrideJob([
                        'fromDate' => $nextDates->getFromDate()->format('Y-m-d H:i:s'),
                        'toDate' => $nextDates->getToDate()->format('Y-m-d H:i:s'),
                        'endDate' => $nextDates->getEndDate()->format('Y-m-d H:i:s'),
                        'stepHours' => $nextDates->getStepHours(),
                        'phones' => $phones,
                    ])
                );
                if ($jobId) {
                    Yii::info([
                        'message' => 'Override Job started',
                        'jobId' => $jobId,
                    ], 'info\CallOverrideController:actionOverridePhoneTo');
                } else {
                    Yii::error([
                        'message' => 'Override Job is not started',
                    ], 'CallOverrideController:actionOverridePhoneTo');
                }
            }
        }

        $timeEnd = microtime(true);
        $time = number_format(round($timeEnd - $timeStart, 2), 2);
        echo Console::renderColoredString('%g --- Execute Time: %w[' . $time . ' s] %g %n'), PHP_EOL;
        echo Console::renderColoredString('%g --- End : %w[' . date('Y-m-d H:i:s') . '] %g' . self::class . ':' . __FUNCTION__ . ' %n'), PHP_EOL;
    }
}

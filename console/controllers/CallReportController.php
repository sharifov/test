<?php

namespace console\controllers;

use sales\model\call\useCase\reports\CallReport;
use sales\model\call\useCase\reports\Credential;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class CallReportController extends Controller
{
    public $date;

    public function options($actionID)
    {
        return ['date'];
    }

    public function actionPriceline()
    {
        printf("\n --- Start %s ---\n\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        if ($this->date === null) {
            $this->date = date('Y-m-d', strtotime('-1 day'));
        } else {
            $this->date = (string)$this->date;
        }

        if (!$this->validateDate($this->date)) {
            echo 'Date is invalid: ' . $this->date . PHP_EOL;
            printf("\n --- End %s ---\n\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $params = \Yii::$app->params['price_line_ftp_credential'];
        $credential = new Credential(
            $params['user'],
            $params['pass'],
            $params['url'],
            $params['port'],
            $params['path']
        );
        $report = new CallReport($credential);

        $phones = [
            '+18559404266',
            '+18559404246',
            '+18559404224',
            '+18559404288',
        ];
        $fileName = 'Call_Priceline_report_' . $this->date . '.csv';

        $report->generate($phones, $fileName, $this->date);
        echo 'OK' . PHP_EOL;

        printf("\n --- End %s ---\n\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        return ExitCode::OK;
    }

    private function validateDate($date, $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}

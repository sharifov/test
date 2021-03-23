<?php

namespace console\controllers;

use sales\model\call\useCase\reports\CallReportSender;
use sales\model\call\useCase\reports\Credential;
use sales\model\call\useCase\reports\DailyReportGenerator;
use sales\model\call\useCase\reports\WeeklyReportGenerator;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;

class CallReportController extends Controller
{
    public function actionPriceline()
    {
        printf(
            "\n --- Start %s ---\n\n",
            $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW)
        );

        $params = \Yii::$app->params['price_line_ftp_credential'];
        $credential = new Credential(
            $params['user'],
            $params['pass'],
            $params['url'],
            $params['port'],
            $params['path']
        );
        $reportSender = new CallReportSender($credential);

        $fileName = 'Call_Priceline_report_' . date('Y-m-d', strtotime('-1 day')) . '.csv';

        $report = (new DailyReportGenerator())->generate();
        $reportSender->send($report, $fileName);

        if ($report) {
            unset($report[0]);
        }

        $rows = [];
        foreach ($report as $result) {
            $rows[] = [
                $result['Time Stamp (UTC)'],
                $result['Call ID'],
                $result['Department'],
                $result['Status'],
                $result['Queue Time'],
                $result['Talk Time'],
                $result['Phone number'],
                $result['Lead'],
                $result['Client id'],
                $result['Trip id'],
            ];
        }

        echo 'Saved in file: ' . $fileName . PHP_EOL;

        echo Table::widget([
            'headers' => [
                'Time Stamp (UTC)',
                'Call ID',
                'Department',
                'Status',
                'Queue Time',
                'Talk Time',
                'Phone number',
                'Lead',
                'Client id',
                'Trip id',
            ],
            'rows' => $rows,
        ]);

        printf("\n --- End %s ---\n\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        return ExitCode::OK;
    }

    public function actionPricelineWeekly()
    {
        printf(
            "\n --- Start %s ---\n\n",
            $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW)
        );

        $params = \Yii::$app->params['price_line_ftp_credential'];
        $credential = new Credential(
            $params['user'],
            $params['pass'],
            $params['url'],
            $params['port'],
            $params['path']
        );
        $reportSender = new CallReportSender($credential);

        $now = strtotime(date('Y-m-d'));
        $fromDate = date('Y-m-d', strtotime('-7 day', $now));
        $toDate = date('Y-m-d', strtotime('-1 day', $now));

        $fileName = 'Weekly Call Report_' . $fromDate . '->' . $toDate . $this->date . '.csv';

        $report = (new WeeklyReportGenerator())->generate();
        $reportSender->send($report, $fileName);

        if ($report) {
            unset($report[0]);
        }

        $rows = [];
        foreach ($report as $result) {
            $rows[] = [
                $result['Date'],
                $result['Queued'],
                $result['Handled'],
                $result['Abandoned'],
                $result['Service Level 30 Sec'],
                $result['Avg Handle Time'],
                $result['Avg Queue Answer Time'],
                $result['Avg Abandon Time'],
            ];
        }

        echo 'Saved in file: ' . $fileName . PHP_EOL;

        echo Table::widget([
            'headers' => ['Date', 'Queued', 'Handled', 'Abandoned', 'Service Level 30 Sec', 'Avg Handle Time', 'Avg Queue Answer Time', 'Avg Abandon Time'],
            'rows' => $rows,
        ]);

        printf("\n --- End %s ---\n\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
        return ExitCode::OK;
    }

    private function validateDate($date, $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}

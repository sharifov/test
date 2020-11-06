<?php

namespace console\controllers;

use sales\model\call\useCase\reports\CallReport;
use sales\model\call\useCase\reports\Credential;
use yii\console\Controller;

class CallReportController extends Controller
{
    public function actionPriceline()
    {
        $phones = [
            '+18559404266',
            '+18559404246',
            '+18559404224',
            '+18559404288',
        ];

        $params = \Yii::$app->params['price_line_ftp_credential'];
        $credential = new Credential(
            $params['user'],
            $params['pass'],
            $params['url'],
            $params['port'],
            $params['path']
        );
        $report = new CallReport($phones, $credential);
        $report->generate();
    }
}

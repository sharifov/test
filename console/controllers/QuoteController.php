<?php
namespace console\controllers;

use common\models\Quote;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Url;
use common\models\EmployeeContactInfo;
use common\components\EmailService;

class QuoteController extends Controller
{

    public function actionSendOpenedNotification($quoteUid)
    {
        printf("\n --- Start %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));

        $quote = Quote::findOne([
            'uid' => $quoteUid
        ]);
        if ($quote == null) {
            printf("\n\Quote not found\n");
        }
        $errors = [];

        $host = \Yii::$app->params['url_address'];

        $lead = $quote->lead;
        $employee = $lead->employee;
        $project_name = $lead->project ? $lead->project->name : "";
        $emailTo = $employee->email;
        $emailFrom = Yii::$app->params['email_from']['sales'];

        $sellerContactInfo = EmployeeContactInfo::findOne([
            'employee_id' => $lead->employee->id,
            'project_id' => $lead->project_id
        ]);
        // $emailFrom = trim($sellerContactInfo->email_user);
        $emailParts = explode('@', $emailFrom);
        $bccEmail = str_replace($emailParts[0], 'supers', $emailFrom);

        $credential = [
            'email' => $emailFrom
        ];

        $subject = \Yii::t('email', "⚐ [Sales] {project_name} Quote-{uid} has been OPENED by client (Lead-{id})", [
            'project_name' => $project_name,
            'uid' => $quote->uid,
            'id' => $lead->id
        ]);
        $body = \Yii::t('email', "Dear agent,<br/><br/>

Your Quote (UID: {quote_uid}) has been OPENED by client!<br/><br/>
Project {project}!<br/><br/>

You can view lead here: {url}<br/><br/>

Regards,<br/>
Sales - Kivork", [
            'url' => $host . '/lead/processing/' . $lead->id,
            'quote_uid' => $quote ? $quote->uid : '-',
            'project' => $project_name
        ]);

        $isSent = EmailService::sendByAWS($emailTo, $lead->project, $credential, $subject, $body, $errors, [
            $bccEmail
        ]);
        if (! $isSent) {
            printf("\n Errors during send message: %s\n", implode(' ', $errors));
        }

        printf("\n --- End %s ---\n", $this->ansiFormat(self::class . ' - ' . $this->action->id, Console::FG_YELLOW));
    }
}
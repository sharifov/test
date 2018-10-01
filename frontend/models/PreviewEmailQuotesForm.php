<?php
namespace frontend\models;

use common\components\EmailService;
use common\models\Employee;
use common\models\EmployeeContactInfo;
use common\models\Lead;
use common\models\LeadLog;
use common\models\local\LeadLogMessage;
use common\models\Project;
use yii\base\Model;

class PreviewEmailQuotesForm extends Model
{

    public $subject;

    public $body;

    public $leadId;

    public $email;

    public $quotes;

    public function rules()
    {
        return [
            [
                [
                    'subject',
                    'body',
                    'leadId'
                ],
                'required'
            ],
            [
                [
                    'subject',
                    'body'
                ],
                'trim'
            ],
            [
                [
                    'quotes'
                ],
                'safe'
            ],
            [
                [
                    'email'
                ],
                'email'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'body' => 'Body',
            'subject' => 'Subject'
        ];
    }

    public function sendEmail(Lead $lead)
    {
        $sellerContactInfo = EmployeeContactInfo::findOne([
            'employee_id' => $lead->employee->id,
            'project_id' => $lead->project_id
        ]);

        $credential = [
            'email' => trim($sellerContactInfo->email_user),
            'password' => $sellerContactInfo->email_pass
        ];

        $errors = [];
        $bcc = [
            trim($sellerContactInfo->email_user),
            'damian.t@wowfare.com',
            'andrew.t@wowfare.com'
        ];
        $isSend = EmailService::sendByAWS($this->email, $lead->project, $credential, $this->subject, $this->body, $errors, $bcc);
        $message = ($isSend) ? sprintf('Sending email - \'Offer\' succeeded! <br/>Emails: %s <br/>Quotes: %s', implode(', ', [
            $this->email
        ]), $this->quotes) : sprintf('Sending email - \'Offer\' failed! <br/>Emails: %s <br/>Quotes: %s', implode(', ', [
            $this->email
        ]), $this->quotes);

        // Add logs after changed model attributes
        $leadLog = new LeadLog((new LeadLogMessage()));
        $leadLog->logMessage->message = empty($errors) ? $message : sprintf('%s <br/>Errors: %s', $message, print_r($errors, true));
        $leadLog->logMessage->title = 'Send Quotes by Email';
        $leadLog->logMessage->model = $this->formName();
        $leadLog->addLog([
            'lead_id' => $lead->id
        ]);

        $result['status'] = $isSend;
        $result['errors'] = $errors;

        return $result;
    }
}
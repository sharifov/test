<?php
namespace frontend\models;

use common\components\EmailService;
use common\models\Client;
use common\models\Employee;
use common\models\EmployeeContactInfo;
use common\models\Lead;
use common\models\LeadLog;
use common\models\local\LeadLogMessage;
use common\models\Project;
use common\models\ProjectEmailTemplate;
use common\models\UserProjectParams;
use yii\base\Model;

class SendEmailForm extends Model
{
    /**
     * @var $employee Employee
     */
    public $employee;
    /**
     * @var $project Project
     */
    public $project;
    public $type;
    public $emailTo;
    public $subject;
    public $body;
    public $extraBody;

    public function rules()
    {
        return [
            [['employee', 'project', 'subject', 'body', 'emailTo', 'type'], 'required'],
            [['emailTo'], 'email'],
            ['extraBody', 'safe'],
            [['subject', 'emailTo', 'body', 'extraBody'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => 'Template Type',
            'body' => 'Body',
            'extraBody' => 'Body',
            'subject' => 'Subject',
            'emailTo' => 'Send To'
        ];
    }

    /**
     * @return ProjectEmailTemplate|null
     */
    public function getTemplate()
    {
        foreach ($this->project->getEmailTemplates() as $emailTemplate) {
            if ($emailTemplate->type == $this->type) {
                return $emailTemplate;
            }
        }
        return null;
    }


    /**
     * @param ProjectEmailTemplate $template
     * @param Client $client
     * @param UserProjectParams $userProjectParams
     */
    public function populate(ProjectEmailTemplate $template, Client $client, UserProjectParams $userProjectParams) : void
    {
        $additionalParams = [];
        if ($template->type == ProjectEmailTemplate::TYPE_SALES_FREE_FORM) {
            $additionalParams['subject'] = $this->subject;
            $additionalParams['body'] = $this->extraBody;
        } elseif ($template->type == ProjectEmailTemplate::TYPE_SALES_TESTIMONIALS) {
            $additionalParams['airlineName'] = 'airline';
        }
        $info = self::viewTemplate($template, $client, $this->project, $userProjectParams, $additionalParams);
        $this->subject = $info['subject'];
        $this->body = $info['body'];
    }

    /**
     * @param ProjectEmailTemplate $template
     * @param Client $client
     * @param Project $source
     * @param UserProjectParams $userProjectParams
     * @param array $additionalParams
     * @return array
     */
    public static function viewTemplate(ProjectEmailTemplate $template, Client $client, Project $source, UserProjectParams $userProjectParams, $additionalParams = []) : array
    {
        $bodyParams = $subjectParams = [];

        switch ($template->type) {
            case ProjectEmailTemplate::TYPE_SALES_FREE_FORM:
                $bodyParams = [
                    'clientFirstName' => ($client->first_name == 'Client Name')
                        ? 'Customer'
                        : ucfirst($client->first_name),
                    'agentName' => ucfirst($userProjectParams->uppUser->username),
                    'agentPhone' => $userProjectParams->upp_phone_number,
                    'projectPhone' => $source->contactInfo->phone,
                    'projectLink' => $source->link,
                    'body' => empty($additionalParams['body'])
                        ? '{body}' : $additionalParams['body']
                ];
                $subjectParams = [
                    'subject' => $additionalParams['subject']
                ];
                break;
            case ProjectEmailTemplate::TYPE_SALES_DID_YOU_GET_MY_QUOTE:
            case ProjectEmailTemplate::TYPE_SALES_CONTACT_SHARE:
            case ProjectEmailTemplate::TYPE_SALES_ADDITIONAL_INFO:
                $bodyParams = [
                    'clientFirstName' => ($client->first_name == 'Client Name')
                        ? 'Customer'
                        : ucfirst($client->first_name),
                    'agentName' => ucfirst($userProjectParams->uppUser->username),
                    'agentPhone' => $userProjectParams->upp_phone_number,
                    'projectPhone' => $source->contactInfo->phone,
                    'projectLink' => $source->link
                ];
                break;
            case ProjectEmailTemplate::TYPE_SALES_TESTIMONIALS:
                $bodyParams = [
                    'clientFirstName' => ($client->first_name == 'Client Name')
                        ? 'Customer'
                        : ucfirst($client->first_name),
                    'agentName' => ucfirst($userProjectParams->uppUser->username),
                    'agentPhone' => $userProjectParams->upp_phone_number,
                    'projectPhone' => $source->contactInfo->phone,
                    'projectLink' => $source->link,
                    'airlineName' => $additionalParams['airlineName']
                ];
                break;
        }

        return [
            'body' => ProjectEmailTemplate::getMessageBody($template->template, $bodyParams),
            'subject' => ProjectEmailTemplate::getMessageBody($template->subject, $subjectParams)
        ];
    }

    /**
     * @param Lead $lead
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function sentEmail(Lead $lead)
    {
        $userProjectParams = UserProjectParams::findOne([
            'upp_user_id' => $this->employee->id,
            'upp_project_id' => $this->project_id
        ]);

        $credential = [
            'email' => $userProjectParams->upp_email,
        ];
        $errors = [];
        $result = EmailService::sendByAWS($this->emailTo, $this->project, $credential, $this->subject, $this->body,$errors);
        $message = ($result)
            ? sprintf('Sending email - \'%s\' succeeded! <br/>Emails: %s', $this->subject, implode(', ', [$this->emailTo]))
            : sprintf('Sending email - \'%s\' failed! <br/>Emails: %s', $this->subject, implode(', ', [$this->emailTo]));

        //Add logs after changed model attributes
        $leadLog = new LeadLog((new LeadLogMessage()));
        $leadLog->logMessage->message = empty($errors)
            ? $message
            : sprintf('%s <br/>Errors: %s', $message, print_r($errors, true));
        $leadLog->logMessage->title = 'Send Info Email';
        $leadLog->logMessage->model = $lead->formName();
        $leadLog->addLog([
            'lead_id' => $lead->id,
        ]);

        return $result;
    }
}
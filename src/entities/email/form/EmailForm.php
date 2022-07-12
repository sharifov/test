<?php

namespace src\entities\email\form;

use src\forms\CompositeForm;
use src\entities\email\helpers\EmailStatus;
use src\entities\email\helpers\EmailType;
use src\entities\email\helpers\EmailFilterType;
use src\entities\email\helpers\EmailContactType;
use src\entities\email\Email;
use common\models\Project;
use common\models\Employee;

/**
 * Class EmailForm
 *
 * @package src\entities\email\form
 *
 * @property EmailParamsForm $params
 * @property EmailBodyForm $body
 * @property EmailLogForm $log
 * @property EmailContactForm[] $contacts
 *
 * @property int $type
 * @property int $status
 * @property int $projectId
 * @property int $depId
 * @property int $emailId
 * @property array $cases
 * @property array $clients
 * @property array $leads
 * @property string $createdDt
 * @property string $updatedDt
 * @property int $userId
 *
 */
class EmailForm extends CompositeForm
{
    public $type;
    public $status;
    public $projectId;
    public $depId;
    public $emailId;
    public $cases;
    public $clients;
    public $leads;
    public $createdDt;
    public $updatedDt;
    public $isDeleted;
    public $replyId;

    private $userId;

    public function __construct(?int $userId = null, $config = [])
    {
        $this->userId = $userId;
        $this->status = EmailStatus::NEW;
        $this->type = EmailType::DRAFT;

        $this->params = new EmailParamsForm();
        $this->body = new EmailBodyForm();
        $this->log = new EmailLogForm();

        $this->contacts = [
            'from' => new EmailContactForm(EmailContactType::FROM),
            'to' => new EmailContactForm(EmailContactType::TO),
        ];

        parent::__construct($config);
    }

    public static function fromArray(array $data)
    {
        $instance = new static();
        $instance->userId = $data['userId'] ?? null;
        $instance->status = $data['status'] ?? EmailStatus::NEW;
        $instance->type = $data['type'] ?? EmailType::DRAFT;
        $instance->projectId = $data['projectId'] ?? null;
        $instance->depId = $data['depId'] ?? null;
        $instance->emailId = $data['emailId'] ?? null;
        $instance->isDeleted = $data['isDeleted'] ?? null;
        $instance->clients = $data['clientsIds'] ?? null;
        $instance->cases = $data['casesIds'] ?? null;
        $instance->leads = $data['leadsIds'] ?? null;
        $instance->createdDt = $data['createdDt'] ?? null;
        $instance->updatedDt = $data['updatedDt'] ?? null;

        $instance->params = EmailParamsForm::fromArray($data['params'] ?? []);
        $instance->body = EmailBodyForm::fromArray($data['body'] ?? []);
        $instance->log = EmailLogForm::fromArray($data['log'] ?? []);

        $from = (isset($data['contacts']['from'])) ? EmailContactForm::fromArray($data['contacts']['from']) : new EmailContactForm(EmailContactType::FROM);
        $to = (isset($data['contacts']['to'])) ? EmailContactForm::fromArray($data['contacts']['to']) : new EmailContactForm(EmailContactType::TO);
        $contactsForm = [
            'from' => $from,
            'to' => $to,
        ];
        $instance->contacts = $contactsForm;

        return $instance;
    }

    public static function fromModel(Email $email, ?int $userId = null, $config = [])
    {
        $instance = new static($userId, $config);
        $instance->status = $email->e_status_id;
        $instance->type = $email->e_type_id;
        $instance->projectId = $email->e_project_id;
        $instance->depId = $email->e_departament_id;
        $instance->emailId = $email->e_id ?? null;
        $instance->clients = $email->clientsIds ?? null;
        $instance->cases = $email->casesIds ?? null;
        $instance->leads = $email->leadsIds ?? null;
        $instance->createdDt = $email->e_created_dt ?? null;
        $instance->updatedDt = $email->e_updated_dt_dt ?? null;

        $instance->params = $email->params ? EmailParamsForm::fromModel($email->params, $config) : new EmailParamsForm();
        $instance->body = EmailBodyForm::fromModel($email->emailBody, $config);
        $instance->log = $email->emailLog ? EmailLogForm::fromModel($email->emailLog, $config) : new EmailLogForm();

        $from = EmailContactForm::fromModel($email->emailContactFrom);
        $to = EmailContactForm::fromModel($email->emailContactTo);
        $contactsForm = [
            'from' => $from,
            'to' => $to,
        ];
        $instance->contacts = $contactsForm;

        return $instance;
    }

    public static function replyFromModel(Email $email, Employee $user, $config = [])
    {
        $instance = new static($user->id, $config);
        $instance->projectId = $email->e_project_id;
        $instance->depId = $email->e_departament_id;
        $instance->replyId = $email->e_id;

        $instance->params = $email->params ? EmailParamsForm::replyFromModel($email->params, $config) : new EmailParamsForm();
        $instance->body = EmailBodyForm::replyFromModel($email->emailBody, $user->username, $config);
        $instance->log = $email->emailLog ? EmailLogForm::replyFromModel($email->emailLog, $config) : new EmailLogForm();

        $from = EmailContactForm::fromArray([
            'email' => $email->emailContactTo->address->ea_email,
            'name'  => $email->emailContactTo->address->ea_name,
            'type'  => EmailContactType::FROM,
        ]);
        $to = EmailContactForm::fromArray([
            'email' => $email->emailContactFrom->address->ea_email,
            'name'  => $email->emailContactFrom->address->ea_name,
            'type'  => EmailContactType::TO,
        ]);
        $contactsForm = [
            'from' => $from,
            'to' => $to,
        ];
        $instance->contacts = $contactsForm;

        return $instance;
    }

    public function rules(): array
    {
        return [
            [['clients', 'cases', 'leads', 'createdDt', 'updatedDt'], 'safe'],
            [['type', 'status', 'projectId', 'depId', 'emailId', 'isDeleted', 'replyId'], 'integer'],
            [['type', 'status', 'projectId'], 'required'],
        ];
    }

    public function listFilterType(): array
    {
        return EmailFilterType::getList();
    }

    public function listProjects(): array
    {
        return Project::getListByUser($this->userId);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function internalForms(): array
    {
        return ['params', 'body', 'contacts', 'log'];
    }
}

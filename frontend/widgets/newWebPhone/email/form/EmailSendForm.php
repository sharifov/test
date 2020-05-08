<?php

namespace frontend\widgets\newWebPhone\email\form;

use common\models\Client;
use common\models\Employee;
use common\models\UserProjectParams;
use sales\model\sms\useCase\send\Contact;
use yii\base\Model;

/**
 * Class EmailSendForm
 *
 * @property string $userEmail
 * @property int $contactId
 * @property string $contactEmail
 * @property int $contactType
 * @property Client|Employee $contactEntity
 * @property Employee $user
 * @property int|null $projectId
 * @property Contact $contact
 * @property string $subject
 * @property string $text
 */
class EmailSendForm extends Model
{
    public $userEmail;
    public $contactId;
    public $contactEmail;
    public $contactType;
    public $contactEntity;
    public $user;
    public $subject;
    public $text;

    private $contact;
    private $projectId;

    public function __construct(Employee $user, $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['userEmail', 'required'],
            ['userEmail', 'string'],
            ['userEmail', 'validateUserEmail', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['contactType', 'required'],
            ['contactType', 'integer'],
            ['contactType', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            //['contactType', 'in', 'range' => [Client::TYPE_CLIENT, Client::TYPE_INTERNAL]],

            ['contactId', 'required'],
            ['contactId', 'integer'],
            ['contactId', 'validateContact', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['contactEmail', 'required'],
            ['contactEmail', 'string'],
            ['contactEmail', 'validateContactEmail', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['subject', 'required'],
            ['subject', 'string', 'max' => 255],

            ['text', 'required'],
            ['text', 'string', 'max' => 65500],
        ];
    }

    public function validateUserEmail(): void
    {
        $upp = UserProjectParams::find()
            ->select(['el_email', 'upp_email_list_id', 'upp_project_id'])
            ->byUserId($this->user->id)
            ->byEmail($this->userEmail, false)
            ->limit(1)
            ->one();

        if (!$upp) {
            $this->addError('userEmail', 'User email not found.');
            return;
        }

        $this->projectId = $upp->upp_project_id;
    }

    public function validateContact(): void
    {
        if ($this->hasErrors()) {
            return;
        }

        if ($this->contactType === Client::TYPE_CLIENT) {
            if (!$this->contactEntity = Client::findOne($this->contactId)) {
                $this->addError('contactId', 'Contact not found.');
            }
            $this->contact = new Contact($this->contactEntity);
            return;
        }

        if ($this->contactType === Client::TYPE_INTERNAL) {
            if (!$this->contactEntity = Employee::findOne($this->contactId)) {
                $this->addError('contactId', 'Contact not found.');
            }
            $this->contact = new Contact($this->contactEntity);
            return;
        }
    }

    public function validateContactEmail(): void
    {
        if ($this->hasErrors()) {
            return;
        }

        if ($this->contactType === Client::TYPE_CLIENT) {
            foreach ($this->contactEntity->clientEmails as $email) {
                if ($email->email === $this->contactEmail) {
                    return;
                }
            }
            $this->addError('contactEmail', 'Contact email not found.');
            return;
        }

        if ($this->contactType === Client::TYPE_INTERNAL) {
            foreach (Employee::getEmailList($this->contactId) as $email) {
                if ($email === $this->contactEmail) {
                    return;
                }
            }
            $this->addError('contactEmail', 'Contact email not found.');
            return;
        }
    }

    public function getContactType(): int
    {
        return $this->contactType;
    }

    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    public function getContactId(): int
    {
        return $this->contact->getId();
    }

    public function getContactName(): ?string
    {
        return $this->contact->getName();
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }

    public function contactIsClient(): bool
    {
        return $this->contactType === Client::TYPE_CLIENT;
    }

    public function contactIsInternal(): bool
    {
        return $this->contactType === Client::TYPE_INTERNAL;
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public function formName(): string
    {
        return '';
    }
}

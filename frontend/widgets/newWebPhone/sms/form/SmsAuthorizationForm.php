<?php

namespace frontend\widgets\newWebPhone\sms\form;

use common\models\Client;
use common\models\Employee;
use common\models\UserProjectParams;
use sales\model\sms\useCase\send\Contact;
use yii\base\Model;

/**
 * Class SmsAuthorizationForm
 *
 * @property string $userPhone
 * @property int $contactId
 * @property string $contactPhone
 * @property int $contactType
 * @property Client|Employee $contactEntity
 * @property Employee $user
 * @property int|null $projectId
 * @property Contact $contact
 */
class SmsAuthorizationForm extends Model
{
    public const CLIENT_TYPES = [
        Client::TYPE_CONTACT,
        Client::TYPE_INTERNAL
    ];

    public $userPhone;
    public $contactId;
    public $contactPhone;
    public $contactType;
    public $contactEntity;
    public $user;

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
            ['userPhone', 'required'],
            ['userPhone', 'string'],
            ['userPhone', 'validateUserPhone', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['contactType', 'required'],
            ['contactType', 'integer'],
            ['contactType', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['contactType', 'in', 'range' => self::CLIENT_TYPES],

            ['contactId', 'required'],
            ['contactId', 'integer'],
            ['contactId', 'validateContact', 'skipOnError' => true, 'skipOnEmpty' => true],

            ['contactPhone', 'required'],
            ['contactPhone', 'string'],
            ['contactPhone', 'validateContactPhone', 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    public function validateUserPhone(): void
    {
        $upp = UserProjectParams::find()
            ->select(['pl_phone_number', 'upp_phone_list_id', 'upp_project_id'])
            ->byUserId($this->user->id)
            ->byPhone($this->userPhone, false)
            ->limit(1)
            ->one();

        if (!$upp) {
            $this->addError('userPhone', 'User phone not found.');
            return;
        }

        $this->projectId = $upp->upp_project_id;
    }

    public function validateContact(): void
    {
        if ($this->hasErrors()) {
            return;
        }

        if ($this->contactType === Client::TYPE_CONTACT) {
            if (!$this->contactEntity = Client::find()->byId($this->contactId)->byContact()->limit(1)->one()) {
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

    public function validateContactPhone(): void
    {
        if ($this->hasErrors()) {
            return;
        }

        if ($this->contactType === Client::TYPE_CONTACT) {
            foreach ($this->contactEntity->clientPhones as $phone) {
                if ($phone->phone === $this->contactPhone) {
                    return;
                }
            }
            $this->addError('contactPhone', 'Contact phone not found.');
            return;
        }

        if ($this->contactType === Client::TYPE_INTERNAL) {
            foreach (Employee::getPhoneList($this->contactId) as $phone) {
                if ($phone === $this->contactPhone) {
                    return;
                }
            }
            $this->addError('contactPhone', 'Contact phone not found.');
            return;
        }
    }

    public function getContactType(): int
    {
        return $this->contactType;
    }

    public function getContactPhone(): string
    {
        return $this->contactPhone;
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

    public function contactIsContact(): bool
    {
        return $this->contactType === Client::TYPE_CONTACT;
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

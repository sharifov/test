<?php

namespace frontend\widgets\newWebPhone\sms\form;

use common\models\Client;
use common\models\Employee;
use common\models\UserProjectParams;
use yii\base\Model;

/**
 * Class SmsAuthorizationForm
 *
 * @property string $userPhone
 * @property int $contactId
 * @property string $contactPhone
 * @property Client $contact
 * @property Employee $user
 * @property int|null $projectId
 */
class SmsAuthorizationForm extends Model
{
    public $userPhone;
    public $contactId;
    public $contactPhone;
    public $contact;

    private $user;
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
        if (!$this->contact = Client::findOne($this->contactId)) {
            $this->addError('contactId', 'Contact not found.');
        }
    }

    public function validateContactPhone(): void
    {
        if ($this->hasErrors()) {
            return;
        }

        foreach ($this->contact->clientPhones as $phone) {
            if ($phone->phone === $this->contactPhone) {
                return;
            }
        }

        $this->addError('contactPhone', 'Contact phone not found.');
    }

    public function getContactPhone(): string
    {
        return $this->contactPhone;
    }

    public function formName(): string
    {
        return '';
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }
}

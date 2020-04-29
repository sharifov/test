<?php

namespace frontend\models\form;

use sales\forms\CompositeForm;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;

/**
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $created
 * @property string $updated
 * @property string $uuid
 * @property string $full_name
 * @property int $parent_id
 * @property bool $is_company
 * @property bool $is_public
 * @property string $company_name
 * @property string $description
 * @property bool $disabled
 * @property int $rating
 * @property int $cl_type_id
 *
 * @property int|null $userId
 * @property EmailCreateForm[] $emails
 * @property PhoneCreateForm[] $phones
 */
class ContactForm extends CompositeForm
{
    public $clientPhone;
    public $clientEmail;

    private $userId;

    public $first_name;
    public $middle_name;
    public $last_name;
    public $company_name;
    public $description;
    public $is_company;
    public $is_public;
    public $disabled;
    public $parent_id;
    public $rating;
    public $cl_type_id;

    /**
     * ContactForm constructor.
     * @param int $countEmails
     * @param int $countPhones
     * @param int|null $userId
     * @param array $config
     */
    public function __construct(int $countEmails = 1, int $countPhones = 1, ?int $userId = null, $config = [])
    {
        $this->emails = array_map(function () {
            return new EmailCreateForm();
        }, self::createCountMultiField($countEmails));

        $this->phones = array_map(function () {
            return new PhoneCreateForm();
        }, self::createCountMultiField($countPhones));

        $this->userId = $userId;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['created', 'updated', 'ucl_favorite', 'emails', 'phones',], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 100],
            [['company_name'], 'string', 'max' => 150],
            [['description'], 'string'],
            [['is_company', 'is_public', 'disabled'], 'boolean'],
            [['parent_id', 'rating', 'cl_type_id'], 'integer'],
        ];
    }

    /**
     * @param null $attributeNames
     * @param bool $clearErrors
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        if (parent::validate($attributeNames, $clearErrors)) {
            $this->checkEmptyPhones();
            $this->checkEmptyEmails();
            return true;
        }
        $this->checkEmptyPhones();
        $this->checkEmptyEmails();
        return false;
    }

    private function checkEmptyPhones(): void
    {
        $errors = false;
        foreach ($this->phones as $key => $phone) {
            if ($key > 0 && !$phone->phone) {
                if (!$this->getErrors('phones.' . $key . '.phone')) {
                    $errors = true;
                    $this->addError('phones.' . $key . '.phone', 'Phone cannot be blank.');
                }
            }
        }
        if (!$errors && count($this->phones) > 1 && isset($this->phones[0]->phone) && !$this->phones[0]->phone) {
            if (!$this->getErrors('phones.0.phone')) {
                $this->addError('phones.0.phone', 'Phone cannot be blank.');
            }
        }
    }

    private function checkEmptyEmails(): void
    {
        $errors = false;
        foreach ($this->emails as $key => $email) {
            if ($key > 0 && !$email->email) {
                if (!$this->getErrors('emails.' . $key . '.email')) {
                    $errors = true;
                    $this->addError('emails.' . $key . '.email', 'Email cannot be blank.');
                }
            }
        }
        if (!$errors && count($this->emails) > 1 && isset($this->emails[0]->email) && !$this->emails[0]->email) {
            if (!$this->getErrors('emails.0.email')) {
                $this->addError('emails.0.email', 'Email cannot be blank.');
            }
        }
    }

    public function internalForms(): array
    {
        return ['emails', 'phones',];
    }
}

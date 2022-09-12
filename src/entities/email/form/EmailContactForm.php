<?php

namespace src\entities\email\form;

use yii\base\Model;
use src\entities\email\helpers\EmailContactType;
use src\entities\email\EmailContact;

class EmailContactForm extends Model
{
    public $id;
    public $email;
    public $emails;
    public $name;
    public $type;

    public function __construct(?int $type = null, $config = [])
    {
        $this->type = $type;
        parent::__construct($config);
    }

    public static function fromArray(array $data): EmailContactForm
    {
        $instance = new static();
        $instance->setAttributes($data);
        return $instance;
    }

    public static function fromModel(EmailContact $contact, $config = []): EmailContactForm
    {
        $instance = new static($contact->ec_type_id, $config);
        $instance->email = $contact->address->ea_email;
        $instance->name = $contact->address->ea_name;
        $instance->id = $contact->ec_id;

        return $instance;
    }

    public function rules(): array
    {
        return [
            [['type'], 'required'],
            [['email', 'name'], 'string'],
            [['emails'], 'default'],
            ['emails', 'safe'],
            ['email',
                'required',
                'isEmpty' => function ($value) {
                    return empty($value);
                },
                'when' => function ($model) {
                    return (empty($model->emails)) && EmailContactType::isRequired($model->type);
                },
            ],
            ['emails',
                'validateEmails',
                'isEmpty' => function ($value) {
                    return empty($value);
                },
                'when' => function ($model) {
                    return EmailContactType::isRequired($model->type);
                },
             ],
            [['type', 'id'], 'integer'],
        ];
    }

    public function validateEmails($attribute, $params)
    {
        if (empty($this->email)) {
            $this->addError($attribute, 'Emails cannot be blank.');
        }
    }

    public function fields(): array
    {
        return [
            'ea_email' => 'email',
            'ea_name' => 'name',
            'ec_type_id' => 'type',
            'ec_id' => 'id',
        ];
    }

    public function attributeLabels(): array
    {
        $typeName = EmailContactType::getName($this->type);
        return [
            'email' => "Email $typeName",
            'name' => "Name $typeName",
        ];
    }

    public function getTypeList(): array
    {
        return EmailContactType::getList();
    }
}

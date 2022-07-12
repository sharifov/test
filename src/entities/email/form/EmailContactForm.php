<?php

namespace src\entities\email\form;

use yii\base\Model;
use src\entities\email\helpers\EmailContactType;
use src\entities\email\EmailContact;

class EmailContactForm extends Model
{
    public $id;
    public $email;
    public $name;
    public $type;

    public function __construct(?int $type = null, $config = [])
    {
        $this->type = $type;
        parent::__construct($config);
    }

    public static function fromArray(array $data)
    {
        $instance = new static();
        $instance->setAttributes($data);
        return $instance;
    }

    public static function fromModel(EmailContact $contact, $config = [])
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
            [['email', 'type'], 'required'],
            [['email', 'name'], 'string'],
            [['type', 'id'], 'integer'],
        ];
    }

    public function fields()
    {
        return [
            'ea_email' => 'email',
            'ea_name' => 'name',
            'ec_type_id' => 'type',
            'ec_id' => 'id',
        ];
    }

    public function attributeLabels()
    {
        $typeName = EmailContactType::getName($this->type);
        return [
            'email' => "Email $typeName",
            'name' => "Name $typeName",
        ];
    }

    public function getTypeList()
    {
        return EmailContactType::getList();
    }
}

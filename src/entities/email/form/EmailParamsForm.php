<?php

namespace src\entities\email\form;

use yii\base\Model;
use src\entities\email\EmailParams;

class EmailParamsForm extends Model
{
    public $id;
    public $templateType;
    public $language;
    public $priority;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public static function fromArray(array $data)
    {
        $instance = new static();
        $instance->setAttributes($data);
        return $instance;
    }

    public static function fromModel(EmailParams $param, $config = [])
    {
        $instance = new static($config);
        $instance->templateType = $param->ep_template_type_id;
        $instance->language = $param->ep_language_id;
        $instance->priority = $param->ep_priority;
        $instance->id = $param->ep_id;

        return $instance;
    }

    public static function replyFromModel(EmailParams $param, $config = [])
    {
        $instance = new static($config);
        $instance->templateType = $param->ep_template_type_id;
        $instance->language = $param->ep_language_id;
        $instance->priority = $param->ep_priority;

        return $instance;
    }

    public function isEmpty()
    {
        foreach ($this->attributes() as $attribute) {
            if (!empty($this->$attribute)) {
                return false;
            }
        }
        return true;
    }

    public function attributes()
    {
        return ['language', 'priority', 'templateType', 'id'];
    }

    public function rules(): array
    {
        return [
            ['language', 'string', 'max' => 5],
            [['priority','templateType','id'], 'integer'],
        ];
    }
}

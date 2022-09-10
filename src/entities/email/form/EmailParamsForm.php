<?php

namespace src\entities\email\form;

use yii\base\Model;
use src\entities\email\EmailParams;
use src\entities\email\helpers\EmailPriority;
use common\models\Language;

class EmailParamsForm extends Model
{
    use FormAttributesTrait;

    public $id;
    public $templateType;
    public $language;
    public $priority;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    public static function fromArray(array $data): EmailParamsForm
    {
        $instance = new static();
        $instance->setAttributes($data);
        return $instance;
    }

    public static function fromModel(EmailParams $param, $config = []): EmailParamsForm
    {
        $instance = new static($config);
        $instance->templateType = $param->ep_template_type_id;
        $instance->language = $param->ep_language_id;
        $instance->priority = $param->ep_priority;
        $instance->id = $param->ep_id;

        return $instance;
    }

    public static function replyFromModel(EmailParams $param, $config = []): EmailParamsForm
    {
        $instance = new static($config);
        $instance->templateType = $param->ep_template_type_id;
        $instance->language = $param->ep_language_id;
        $instance->priority = $param->ep_priority;

        return $instance;
    }

    public function attributes(): array
    {
        return ['language', 'priority', 'templateType', 'id'];
    }


    public function fields(): array
    {
        return [
            'ep_language_id' => 'language',
            'ep_priority' => 'priority',
            'ep_template_type_id' => 'templateType',
            'ep_id' => 'id'
        ];
    }

    public function rules(): array
    {
        return [
            ['language', 'string', 'max' => 5],
            [['priority', 'templateType', 'id'], 'integer'],
        ];
    }

    public function listPriorities(): array
    {
        return EmailPriority::getList();
    }

    public function listLanguages(): array
    {
        return Language::getLanguages(true);
    }
}

<?php

namespace sales\model\call\form;

use common\models\Call;
use yii\base\Model;

/**
 * Class CallCustomParameters
 *
 * @property string $parent_call_sid
 * @property bool $is_conference_call
 * @property int $type_id
 * @property int $source_type_id
 * @property int $project_id
 * @property int $lead_id
 * @property int $case_id
 * @property int $user_id
 * @property int $accepted_call_id
 */
class CallCustomParameters extends Model
{
    public $parent_call_sid;
    public $is_conference_call;
    public $type_id;
    public $source_type_id;
    public $project_id;
    public $lead_id;
    public $case_id;
    public $user_id;
    public $accepted_call_id;

    public function rules(): array
    {
        return [
            ['parent_call_sid', 'string'],

            ['is_conference_call', 'boolean'],

            ['type_id', 'integer'],
            ['type_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['type_id', 'in', 'range' => array_keys(Call::TYPE_LIST)],

            ['source_type_id', 'integer'],
            ['source_type_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['source_type_id', 'in', 'range' => array_keys(Call::SOURCE_LIST)],

            ['project_id', 'integer'],
            ['project_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],

            ['lead_id', 'integer'],

            ['case_id', 'integer'],

            ['user_id', 'integer'],

            ['accepted_call_id', 'integer'],
        ];
    }

    public function resetErrorsAttribute(): void
    {
        foreach ($this->getAttributes() as $key => $attribute) {
            if ($this->getErrors($key)) {
                $this->{$key} = null;
            }
        }
    }

    public function toUrl(): string
    {
        return http_build_query($this->getAttributes());
    }

    public function formName(): string
    {
        return '';
    }
}

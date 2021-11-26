<?php

namespace sales\model\call\useCase\conference\create;

use yii\base\Model;

/**
 * Class CreateCallForm
 *
 * @property string $user_identity
 * @property int $user_id
 * @property string $to_number
 * @property string $from_number
 * @property int $phone_list_id
 * @property int $project_id
 * @property int $department_id
 * @property int $lead_id
 * @property int $case_id
 * @property int $client_id
 * @property int $source_type_id
 * @property bool $call_recording_disabled
 * @property string $friendly_name
 * @property bool $is_redial_call
 */
class CreateCallForm extends Model
{
    public $user_identity;
    public $user_id;
    public $to_number;
    public $from_number;
    public $phone_list_id;
    public $project_id;
    public $department_id;
    public $lead_id;
    public $case_id;
    public $client_id;
    public $source_type_id;
    public $call_recording_disabled;
    public $friendly_name;
    public $is_redial_call;

    public function rules(): array
    {
        return [
            ['user_identity', 'required'],
            ['user_identity', 'string'],

            ['user_id', 'required'],
            ['user_id', 'integer'],

            ['to_number', 'required'],
            ['to_number', 'string'],

            ['from_number', 'required'],
            ['from_number', 'string'],

            ['phone_list_id', 'required'],
            ['phone_list_id', 'integer'],

            ['project_id', 'required'],
            ['project_id', 'integer'],

            ['department_id', 'integer'],

            ['lead_id', 'integer'],

            ['case_id', 'integer'],

            ['client_id', 'integer'],

            ['source_type_id', 'integer'],

            ['call_recording_disabled', 'required'],
            ['call_recording_disabled', 'boolean'],

            ['friendly_name', 'required'],
            ['friendly_name', 'string'],

            ['is_redial_call', 'default', 'value' => false],
            ['is_redial_call', 'boolean'],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

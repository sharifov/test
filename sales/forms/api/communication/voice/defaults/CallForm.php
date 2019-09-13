<?php

namespace sales\forms\api\communication\voice\defaults;

use yii\base\Model;

/**
 * Class CallForm
 * @property $c_id;
 * @property $c_call_sid;
 * @property $c_call_type_id;
 * @property $c_from;
 * @property $c_to;
 * @property $c_call_status;
 * @property $c_parent_call_sid;
 * @property $c_created_dt;
 * @property $c_updated_dt;
 * @property $c_endpoint;
 */
class CallForm extends Model
{

    public $c_id;
    public $c_call_sid;
    public $c_call_type_id;
    public $c_from;
    public $c_to;
    public $c_call_status;
    public $c_parent_call_sid;
    public $c_created_dt;
    public $c_updated_dt;
    public $c_endpoint;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['c_id', 'integer'],
            ['c_call_sid', 'string'],
            ['c_call_type_id', 'integer'],
            ['c_from', 'string'],
            ['c_to', 'string'],
            ['c_call_status', 'in', 'range' => ['no-answer', 'completed']],
            ['c_parent_call_sid', 'string'],
            ['c_created_dt', 'string'],
            ['c_updated_dt', 'string'],
            ['c_endpoint', 'in', 'range' => ['client']],
        ];
    }

}
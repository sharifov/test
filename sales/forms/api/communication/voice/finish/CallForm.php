<?php

namespace sales\forms\api\communication\voice\finish;

use common\models\Call;
use yii\base\Model;

/**
 * Class CallForm
 * @property $c_id
 * @property $c_call_sid;
 * @property $c_call_type_id;
 * @property $c_from;
 * @property $c_to;
 * @property $c_call_status;
 * @property $c_recording_url;
 * @property $c_recording_duration;
 * @property $c_created_dt;
 * @property $c_updated_dt;
 * @property $c_call_duration;
 * @property $c_tw_price;
 * @property $c_endpoint;
 * @property $c_caller_name;
 * @property $c_project_id;
 */
class CallForm extends Model
{

    public $c_id;
    public $c_call_sid;
    public $c_call_type_id;
    public $c_from;
    public $c_to;
    public $c_call_status;
    public $c_recording_url;
    public $c_recording_duration;
    public $c_created_dt;
    public $c_updated_dt;
    public $c_call_duration;
    public $c_tw_price;
    public $c_endpoint;
    public $c_caller_name;
    public $c_project_id;

    /**
     * @return bool
     */
    public function isIncoming(): bool
    {
        return $this->c_call_type_id === Call::CALL_TYPE_IN;
    }

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
            ['c_call_status', 'in', 'range' => ['completed']],
            ['c_recording_url', 'string'],
            ['c_recording_duration', 'integer'],
            ['c_created_dt', 'string'],
            ['c_updated_dt', 'string'],
            ['c_call_duration', 'integer'],
            ['c_tw_price', 'double'],
            ['c_endpoint', 'in', 'range' => ['client']],
            ['c_caller_name', 'string'],
            ['c_project_id', 'integer'],
        ];
    }

}
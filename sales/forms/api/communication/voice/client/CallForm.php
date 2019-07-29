<?php

namespace sales\forms\api\communication\voice\client;

use common\models\Call;
use yii\base\Model;

/**
 * Class CallForm
 * @property $c_id;
 * @property $c_call_sid;
 * @property $c_account_sid;
 * @property $c_project_id;
 * @property $c_call_type_id;
 * @property $c_from;
 * @property $c_to;
 * @property $c_sip;
 * @property $c_call_status;
 * @property $c_api_version;
 * @property $c_direction;
 * @property $c_caller_name;
 * @property $c_uri;
 * @property $c_created_dt;
 * @property $c_endpoint;
 * @property $c_recording_url;
 * @property $c_recording_sid;
 * @property $c_recording_duration;
 */
class CallForm extends Model
{

    public $c_id;
    public $c_call_sid;
    public $c_account_sid;
    public $c_project_id;
    public $c_call_type_id;
    public $c_from;
    public $c_to;
    public $c_sip;
    public $c_call_status;
    public $c_api_version;
    public $c_direction;
    public $c_caller_name;
    public $c_uri;
    public $c_created_dt;
    public $c_endpoint;
    public $c_recording_url;
    public $c_recording_sid;
    public $c_recording_duration;

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
            ['c_account_sid', 'string'],
            ['c_project_id', 'integer'],
            ['c_call_type_id', 'integer'],
            ['c_from', 'string'],
            ['c_to', 'string'],
            ['c_sip', 'string'],
            ['c_call_status', 'in', 'range' => ['ringing']],
            ['c_api_version', 'date', 'format' => 'Y-m-d'],
            ['c_direction', 'in', 'range' => ['outbound-w']],
            ['c_caller_name', 'string'],
            ['c_uri', 'string'],
            ['c_created_dt', 'string'],
            ['c_endpoint', 'in', 'range' => ['number']],
            ['c_recording_url', 'string'],
            ['c_recording_sid', 'string'],
            ['c_recording_duration', 'string'],
        ];
    }

}
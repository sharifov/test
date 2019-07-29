<?php

namespace sales\forms\api\communication\voice\record;

use common\models\Call;
use yii\base\Model;

/**
 * Class CallForm
 * @property  $c_id;
 * @property  $c_call_sid;
 * @property  $c_account_sid;
 * @property  $c_project_id;
 * @property  $c_call_type_id;
 * @property  $c_sip;
 * @property  $c_from;
 * @property  $c_to;
 * @property  $c_caller_name;
 * @property  $c_call_status;
 * @property  $c_api_version;
 * @property  $c_direction;
 * @property  $c_parent_call_sid;
 * @property  $c_recording_url;
 * @property  $c_recording_sid;
 * @property  $c_recording_duration;
 * @property  $c_timestamp;
 * @property  $c_uri;
 * @property  $c_created_dt;
 * @property  $c_updated_dt;
 * @property  $c_endpoint;
 */
class CallForm extends Model
{
    public $c_id;
    public $c_call_sid;
    public $c_account_sid;
    public $c_project_id;
    public $c_call_type_id;
    public $c_sip;
    public $c_from;
    public $c_to;
    public $c_caller_name;
    public $c_call_status;
    public $c_api_version;
    public $c_direction;
    public $c_parent_call_sid;
    public $c_recording_url;
    public $c_recording_sid;
    public $c_recording_duration;
    public $c_timestamp;
    public $c_uri;
    public $c_created_dt;
    public $c_updated_dt;
    public $c_endpoint;

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
            ['c_sip', 'safe'],
            ['c_from', 'safe'],
            ['c_to', 'safe'],
            ['c_caller_name', 'string'],
            ['c_call_status', 'in', 'range' => ['completed', 'in-progress', 'ringing']],
            ['c_api_version', 'date', 'format' => 'Y-m-d'],
            ['c_direction', 'in', 'range' => ['inbound', 'outbound-w']],
            ['c_parent_call_sid', 'string'],
            ['c_recording_url', 'string'],
            ['c_recording_sid', 'string'],
            ['c_recording_duration', 'integer'],
            ['c_timestamp', 'safe'],
            ['c_uri', 'string'],
            ['c_created_dt', 'safe'],
            ['c_updated_dt', 'safe'],
            ['c_endpoint', 'in', 'range' => ['client', 'number']],
        ];
    }

}
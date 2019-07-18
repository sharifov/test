<?php

namespace sales\forms\api\communication\voice\client;

use yii\base\Model;

/**
 * Class CallDataForm
 * @property $ApiVersion;
 * @property $Called;
 * @property $CallStatus;
 * @property $From;
 * @property $To;
 * @property $Direction;
 * @property $AccountSid;
 * @property $ApplicationSid;
 * @property $FromAgentPhone;
 * @property $c_user_id;
 * @property $Caller;
 * @property $project_id;
 * @property $c_type;
 * @property $CallSid;
 * @property $sid;
 * @property $lead_id;
 *
 * @property $price;
 * @property $status;
 */
class CallDataForm extends Model
{

    public $ApiVersion;
    public $Called;
    public $CallStatus;
    public $From;
    public $To;
    public $Direction;
    public $AccountSid;
    public $ApplicationSid;
    public $FromAgentPhone;
    public $c_user_id;
    public $Caller;
    public $project_id;
    public $c_type;
    public $CallSid;
    public $sid;
    public $lead_id;

    public $price;
    public $status;
    public $duration;

    /**
     * @return bool
     */
    public function isEmptyCallSid(): bool
    {
        return $this->sid ? false : true;
    }

    /**
     * @return bool
     */
    public function beforeValidate(): bool
    {
        if (!$this->sid) {
            $this->sid = $this->CallSid;
        }
        return parent::beforeValidate();
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['ApiVersion', 'date', 'format' => 'Y-m-d'],
            ['Called', 'string'],
            ['CallStatus', 'in', 'range'  => ['ringing']],
            ['From', 'string'],
            ['To', 'string'],
            ['Direction', 'in', 'range' => ['inbound']],
            ['AccountSid', 'string'],
            ['ApplicationSid', 'string'],
            ['FromAgentPhone', 'string'],
            ['c_user_id', 'integer'],
            ['Caller', 'string'],
            ['project_id', 'integer'],
            ['c_type', 'in', 'range' => ['web-call']],
            ['CallSid', 'string'],
            ['sid', 'string'],
            ['lead_id', 'integer'],

            ['price', 'double'],
            ['status', 'string'],
            ['duration', 'integer'],
        ];
    }

}
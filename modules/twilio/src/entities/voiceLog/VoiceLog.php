<?php

namespace modules\twilio\src\entities\voiceLog;

use Yii;

/**
 * This is the model class for table "voice_log".
 *
 * @property int $vl_id
 * @property string $vl_call_sid
 * @property string $vl_account_sid
 * @property string|null $vl_from
 * @property string|null $vl_to
 * @property string|null $vl_call_status
 * @property string|null $vl_api_version
 * @property string|null $vl_direction
 * @property string|null $vl_forwarded_from
 * @property string|null $vl_caller_name
 * @property string|null $vl_parent_call_sid
 * @property string|null $vl_call_duration
 * @property string|null $vl_sip_response_code
 * @property string|null $vl_recording_url
 * @property string|null $vl_recording_sid
 * @property string|null $vl_recording_duration
 * @property string|null $vl_timestamp
 * @property string|null $vl_callback_source
 * @property string|null $vl_sequence_number
 * @property string|null $vl_created_dt
 */
class VoiceLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'voice_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['vl_call_sid', 'vl_account_sid'], 'required'],
            [['vl_created_dt'], 'safe'],
            [['vl_call_sid', 'vl_account_sid', 'vl_parent_call_sid', 'vl_recording_sid'], 'string', 'max' => 34],
            [['vl_from', 'vl_to', 'vl_forwarded_from'], 'string', 'max' => 100],
            [['vl_call_status', 'vl_direction'], 'string', 'max' => 15],
            [['vl_api_version', 'vl_sip_response_code'], 'string', 'max' => 10],
            [['vl_caller_name'], 'string', 'max' => 50],
            [['vl_call_duration', 'vl_recording_duration'], 'string', 'max' => 20],
            [['vl_recording_url'], 'string', 'max' => 200],
            [['vl_timestamp', 'vl_callback_source', 'vl_sequence_number'], 'string', 'max' => 40],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'vl_id' => 'Vl ID',
            'vl_call_sid' => 'Vl Call Sid',
            'vl_account_sid' => 'Vl Account Sid',
            'vl_from' => 'Vl From',
            'vl_to' => 'Vl To',
            'vl_call_status' => 'Vl Call Status',
            'vl_api_version' => 'Vl Api Version',
            'vl_direction' => 'Vl Direction',
            'vl_forwarded_from' => 'Vl Forwarded From',
            'vl_caller_name' => 'Vl Caller Name',
            'vl_parent_call_sid' => 'Vl Parent Call Sid',
            'vl_call_duration' => 'Vl Call Duration',
            'vl_sip_response_code' => 'Vl Sip Response Code',
            'vl_recording_url' => 'Vl Recording Url',
            'vl_recording_sid' => 'Vl Recording Sid',
            'vl_recording_duration' => 'Vl Recording Duration',
            'vl_timestamp' => 'Vl Timestamp',
            'vl_callback_source' => 'Vl Callback Source',
            'vl_sequence_number' => 'Vl Sequence Number',
            'vl_created_dt' => 'Vl Created Dt',
        ];
    }

    public static function find()
	{
		return new Scopes(static::class);
	}
}

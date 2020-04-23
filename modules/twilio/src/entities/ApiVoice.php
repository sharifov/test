<?php

namespace modules\twilio\src\entities;

use common\models\Project;
use yii\base\Model;

/**
 * This is the model class for table "voice".
 *
 * @property int $project_id
 * @property string $from
 * @property string $from_number
 * @property string $from_name
 * @property string $to
 * @property array $options
 *
 */
class ApiVoice extends Model
{

    public $project_id;
    public $from;
    public $from_number;
    public $from_name;
    public $to;
    public $options;

    /*
     *  'Url' => $options['url'],
            'ApplicationSid' => $options['applicationSid'],
            'Method' => $options['method'],
            'FallbackUrl' => $options['fallbackUrl'],
            'FallbackMethod' => $options['fallbackMethod'],
            'StatusCallback' => $options['statusCallback'],
            'StatusCallbackEvent' => Serialize::map($options['statusCallbackEvent'], function($e) { return $e; }),
            'StatusCallbackMethod' => $options['statusCallbackMethod'],
            'SendDigits' => $options['sendDigits'],
            'IfMachine' => $options['ifMachine'],
            'Timeout' => $options['timeout'],
            'Record' => Serialize::booleanToString($options['record']),
            'RecordingChannels' => $options['recordingChannels'],
            'RecordingStatusCallback' => $options['recordingStatusCallback'],
            'RecordingStatusCallbackMethod' => $options['recordingStatusCallbackMethod'],
            'SipAuthUsername' => $options['sipAuthUsername'],
            'SipAuthPassword' => $options['sipAuthPassword'],
            'MachineDetection' => $options['machineDetection'],
            'MachineDetectionTimeout' => $options['machineDetectionTimeout'],
            'RecordingStatusCallbackEvent' => Serialize::map($options['recordingStatusCallbackEvent'], function($e) { return $e; }),
            'Trim' => $options['trim'],
            'CallerId' => $options['callerId'],
     */

    public function formName() : string
    {
        return 'voice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from', 'to', 'from_number'], 'required'],
            [['project_id'], 'integer'],
            [['from', 'to', 'from_number', 'from_name'], 'string', 'max' => 50],
            [['options'], 'safe'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'project_id' => 'Project ID',
            'from'      => 'Phone/SIP number From',
            'from_number' => 'Phone number From',
            'from_name' => 'Name',
            'to'        => 'Phone number To',
            'options'   => 'Options',
        ];
    }

}

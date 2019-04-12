<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "call".
 *
 * @property int $c_id
 * @property string $c_call_sid
 * @property string $c_account_sid
 * @property int $c_call_type_id
 * @property string $c_from
 * @property string $c_to
 * @property string $c_sip
 * @property string $c_call_status
 * @property string $c_api_version
 * @property string $c_direction
 * @property string $c_forwarded_from
 * @property string $c_caller_name
 * @property string $c_parent_call_sid
 * @property int $c_call_duration
 * @property string $c_sip_response_code
 * @property string $c_recording_url
 * @property string $c_recording_sid
 * @property string $c_recording_duration
 * @property string $c_timestamp
 * @property string $c_uri
 * @property string $c_sequence_number
 * @property int $c_lead_id
 * @property int $c_created_user_id
 * @property string $c_created_dt
 * @property int $c_com_call_id
 * @property string $c_updated_dt
 * @property int $c_project_id
 * @property string $c_error_message
 * @property bool $c_is_new
 * @property bool $c_is_deleted
 * @property float $c_price
 *
 * @property Employee $cCreatedUser
 * @property Lead $cLead
 * @property Project $cProject
 */
class Call extends \yii\db\ActiveRecord
{

    public const CALL_STATUS_QUEUE          = 'queued';
    public const CALL_STATUS_RINGING        = 'ringing';
    public const CALL_STATUS_IN_PROGRESS    = 'in-progress';
    public const CALL_STATUS_COMPLETED      = 'completed';
    public const CALL_STATUS_BUSY           = 'busy';
    public const CALL_STATUS_FAILED         = 'failed';
    public const CALL_STATUS_NO_ANSWER      = 'no-answer';
    public const CALL_STATUS_CANCELED       = 'canceled';

    public const CALL_STATUS_LIST = [
        self::CALL_STATUS_QUEUE         => 'Queued',
        self::CALL_STATUS_RINGING       => 'Ringing',
        self::CALL_STATUS_IN_PROGRESS   => 'In progress',
        self::CALL_STATUS_COMPLETED     => 'Completed',
        self::CALL_STATUS_BUSY          => 'Busy',
        self::CALL_STATUS_FAILED        => 'Failed',
        self::CALL_STATUS_NO_ANSWER     => 'No answer',
        self::CALL_STATUS_CANCELED      => 'Canceled',
    ];

    public const CALL_STATUS_DESCRIPTION_LIST = [
        self::CALL_STATUS_QUEUE         => 'The call is ready and waiting in line before going out.',
        self::CALL_STATUS_RINGING       => 'The call is currently ringing.',
        self::CALL_STATUS_IN_PROGRESS   => 'The call was answered and is actively in progress.',
        self::CALL_STATUS_COMPLETED     => 'The call was answered and has ended normally.',
        self::CALL_STATUS_BUSY          => 'The caller received a busy signal.',
        self::CALL_STATUS_FAILED        => 'The call could not be completed as dialed, most likely because the phone number was non-existent.',
        self::CALL_STATUS_NO_ANSWER     => 'The call ended without being answered.',
        self::CALL_STATUS_CANCELED      => 'The call was canceled via the REST API while queued or ringing.',
    ];

    public const CALL_TYPE_OUT  = 1;
    public const CALL_TYPE_IN   = 2;

    public const CALL_TYPE_LIST = [
        self::CALL_TYPE_OUT => 'Outgoing',
        self::CALL_TYPE_IN  => 'Incoming',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['c_call_sid'], 'required'],
            [['c_call_type_id', 'c_lead_id', 'c_created_user_id', 'c_com_call_id', 'c_project_id', 'c_call_duration'], 'integer'],
            [['c_price'], 'number'],
            [['c_is_new', 'c_is_deleted'], 'boolean'],
            [['c_created_dt', 'c_updated_dt'], 'safe'],
            [['c_call_sid', 'c_account_sid', 'c_parent_call_sid', 'c_recording_sid'], 'string', 'max' => 34],
            [['c_from', 'c_to', 'c_sip', 'c_forwarded_from'], 'string', 'max' => 100],
            [['c_call_status', 'c_direction'], 'string', 'max' => 15],
            [['c_api_version', 'c_sip_response_code'], 'string', 'max' => 10],
            [['c_caller_name'], 'string', 'max' => 50],
            [['c_recording_duration'], 'string', 'max' => 20],
            [['c_recording_url', 'c_uri'], 'string', 'max' => 200],
            [['c_timestamp', 'c_sequence_number'], 'string', 'max' => 40],
            [['c_error_message'], 'string', 'max' => 500],
            [['c_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['c_created_user_id' => 'id']],
            [['c_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['c_lead_id' => 'id']],
            [['c_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['c_project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'c_id' => 'ID',
            'c_call_sid' => 'Call Sid',
            'c_account_sid' => 'Account Sid',
            'c_call_type_id' => 'Call Type ID',
            'c_from' => 'From',
            'c_to' => 'To',
            'c_sip' => 'Sip',
            'c_call_status' => 'Call Status',
            'c_api_version' => 'Api Version',
            'c_direction' => 'Direction',
            'c_forwarded_from' => 'Forwarded From',
            'c_caller_name' => 'Caller Name',
            'c_parent_call_sid' => 'Parent Call Sid',
            'c_call_duration' => 'Call Duration',
            'c_sip_response_code' => 'Sip Response Code',
            'c_recording_url' => 'Recording Url',
            'c_recording_sid' => 'Recording Sid',
            'c_recording_duration' => 'Recording Duration',
            'c_timestamp' => 'Timestamp',
            'c_uri' => 'Uri',
            'c_sequence_number' => 'Sequence Number',
            'c_lead_id' => 'Lead ID',
            'c_created_user_id' => 'Created User ID',
            'c_created_dt' => 'Created Dt',
            'c_com_call_id' => 'Com Call ID',
            'c_updated_dt' => 'Updated Dt',
            'c_project_id' => 'Project ID',
            'c_error_message' => 'Error Message',
            'c_is_new' => 'Is New',
            'c_is_deleted' => 'Is Deleted',
            'c_price' => 'Price'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'c_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCProject()
    {
        return $this->hasOne(Project::class, ['id' => 'c_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'c_lead_id']);
    }

    /**
     * {@inheritdoc}
     * @return CallQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CallQuery(get_called_class());
    }

    /**
     * @return mixed|string
     */
    public function getCallTypeName()
    {
        return self::CALL_TYPE_LIST[$this->c_call_type_id] ?? '-';
    }

    /**
     * @return mixed|string
     */
    public function getStatusName()
    {
        return self::CALL_STATUS_LIST[$this->c_call_status] ?? '-';
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $users = UserConnection::find()->select('uc_user_id')
            ->andWhere(['uc_controller_id' => 'call', 'uc_action_id' => 'user-map'])
            ->groupBy(['uc_user_id'])->all();

        if($users) {
            foreach ($users as $user) {
                Notifications::socket($user->uc_user_id, null, 'callMapUpdate', [], true);
            }
        }
    }

    public function get_hours_range( $start = 0, $end = 86400, $step = 3600, $format = 'H:i' ) {
        $times = array();
        foreach ( range( $start, $end, $step ) as $timestamp ) {
            $hour_mins = gmdate( 'H:i', $timestamp );
            if ( ! empty( $format ) )
                $times[$hour_mins] = gmdate( $format, $timestamp );
            else $times[$hour_mins] = $hour_mins;
        }
        return $times;
    }

    public function dateRange( $first, $last, $step = '+1 day', $format = 'Y/m/d' ) {

        $dates = array();
        $current = strtotime( $first );
        $last = strtotime( $last );

        while( $current <= $last ) {

            $dates[] = date( $format, $current );
            $current = strtotime( $step, $current );
        }

        return $dates;
    }

    public static function getCallStats($startDate, $endDate)
    {
        $calls = self::find()->select(['c_id', 'c_call_status', 'c_updated_dt'])->where(['c_call_status' => ['completed', 'busy', 'no-answer']])->andWhere(['between', 'c_updated_dt', $startDate." 00:00:00", $endDate." 23:59:59" ])->all();
        $hourlyCallStats = [];
        $item = [];

        $timeRange = self::get_hours_range();
        $dateRange = self::dateRange($startDate, $endDate);
        if (strtotime($startDate) < strtotime($endDate)){
            $timeLine = $dateRange;
            /*foreach ($timeLine as $val){
                echo $val; echo '<br>';
            }
            die();*/
            $item['timeLine'] = 'd M';
        } else {
            $timeLine = $timeRange;
            $item['timeLine'] = 'H:i';
        }
        $completed = $noAnswer = $busy = 0;
        foreach ($timeLine as $key => $hour){

            $hourEndPoint = date('H:i', strtotime($hour) + 3600);
            foreach ($calls as $callItem){
                $callUpdatedTime = date('H:i', strtotime($callItem->c_updated_dt));
                if ($callUpdatedTime >= $hour && $callUpdatedTime <= $hourEndPoint)
                {
                    switch ($callItem->c_call_status){
                        case 'completed':
                            $completed++;
                            break;
                        case 'no-answer':
                            $noAnswer++;
                            break;
                        case 'busy':
                            $busy++;
                            break;
                    }
                }
            }
            $item['time'] = $hour;
            $item['completed'] = $completed;
            $item['no-answer'] = $noAnswer;
            $item['busy'] = $busy;

            array_push($hourlyCallStats, $item);
            $completed = $noAnswer = $busy = 0;
            //echo $hour; echo '<br>';
        }
        return $hourlyCallStats;
    }
}

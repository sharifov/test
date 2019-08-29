<?php

namespace common\models;

use common\components\jobs\TelegramSendMessageJob;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\queue\beanstalk\Queue;

/**
 * This is the model class for table "notifications".
 *
 * @property integer $n_id
 * @property integer $n_user_id
 * @property integer $n_type_id
 * @property string $n_title
 * @property string $n_message
 * @property boolean $n_new
 * @property boolean $n_deleted
 * @property boolean $n_popup
 * @property boolean $n_popup_show
 * @property string $n_read_dt
 * @property string $n_created_dt
 * @property string $n_unique_id
 *
 * @property Employee $nUser
 */
class Notifications extends ActiveRecord
{

    CONST TYPE_SUCCESS = 1;
    CONST TYPE_INFO = 2;
    CONST TYPE_WARNING = 3;
    CONST TYPE_DANGER = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notifications';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['n_user_id', 'n_type_id'], 'required'],
            [['n_user_id', 'n_type_id'], 'integer'],
            [['n_message'], 'string'],
            [['n_new', 'n_deleted', 'n_popup', 'n_popup_show'], 'boolean'],
            [['n_read_dt', 'n_created_dt'], 'safe'],
            [['n_title'], 'string', 'max' => 100],
            [['n_unique_id'], 'string', 'max' => 40],
            [['n_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['n_user_id' => 'id']],
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['n_created_dt'],
                    //ActiveRecord::EVENT_BEFORE_UPDATE => ['n_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'n_id' => 'ID',
            'n_user_id' => 'User',
            'n_type_id' => 'Type ID',
            'n_title' => 'Title',
            'n_message' => 'Message',
            'n_new' => 'New',
            'n_deleted' => 'Deleted',
            'n_popup' => 'Popup',
            'n_popup_show' => 'Popup Show',
            'n_read_dt' => 'Read Date',
            'n_created_dt' => 'Created Date',
            'n_unique_id' => 'Unique ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'n_user_id']);
    }

    /**
     * @inheritdoc
     * @return NotificationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NotificationsQuery(get_called_class());
    }

    /**
     * @return mixed
     */
    public static function getTypeList()
    {
        $list[self::TYPE_SUCCESS]   = 'Success';
        $list[self::TYPE_INFO]      = 'Info';
        $list[self::TYPE_WARNING]   = 'Warning';
        $list[self::TYPE_DANGER]    = 'Danger';

        return $list;
    }

    /**
     * @param integer $type_id
     * @return string
     */
    public function getType($type_id = null)
    {
        if(!$type_id) $type_id = $this->n_type_id;

        $list = self::getTypeList();
        if(isset($list[$type_id])) return $list[$type_id];
            else return '???';
    }

    /**
     * @param int $user_id
     * @return mixed
     */
    public static function findNewCount($user_id = 0)
    {
        $cnt = self::find()->where(['n_user_id' => $user_id, 'n_new' => true, 'n_deleted' => false])->count();
        return $cnt;
    }

    /**
     * @param int $user_id
     * @return array|Notifications[]
     */
    public static function findNew($user_id = 0)
    {
        $list = self::find()->where(['n_user_id' => $user_id, 'n_new' => true, 'n_deleted' => false])->all();
        return $list;
    }

    /**
     * @return string
     */
    public function getNotifyType()
    {
        switch ($this->n_type_id) {
            case self::TYPE_SUCCESS: $str = 'success';
                break;
            case self::TYPE_INFO: $str = 'info';
                break;
            case self::TYPE_WARNING: $str = '';
                break;
            case self::TYPE_DANGER: $str = 'error';
                break;
            default: $str = 'info';
        }
        return $str;
    }


    /**
     * @param int $user_id
     * @param string $title
     * @param string $message
     * @param int $type
     * @param bool $popup
     * @param bool $unique
     * @return bool
     */
    public static function create($user_id = 0, $title = '', $message = '', $type = 1, $popup = true, $unique = false) : bool
    {

        $md5Hash = md5($message.$user_id);
        if($unique) {
            $exists = self::find()->where(['n_unique_id' => $md5Hash])->exists();
            if($exists) {
                return false;
            }
        }

        $model = new self();
        $model->n_user_id = $user_id;
        $model->n_title = $title;
        $model->n_message = $message;
        $model->n_type_id = $type;
        $model->n_popup = $popup;
        $model->n_unique_id = $md5Hash;

        $model->n_new = true;
        if($model->save()){
            return true;
        }

        return false;
    }


    public function afterSave($insert, $changedAttributes)
    {

        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            $job = new TelegramSendMessageJob();
            $job->user_id = $this->n_user_id;
            $job->text = $this->n_message;

            $queue = Yii::$app->queue_job;
            $jobId = $queue->push($job);
            //Yii::info('UserID: '.$job->user_id.', TelegramSendMessageJob: '.$jobId, 'info\Notifications:afterSave:TelegramSendMessageJob');
        }
    }


    /**
     * @param int|null $user_id
     * @param int|null $lead_id
     * @param string|null $command
     * @param array $data
     * @param bool $multiple
     * @return bool
     */
    public static function socket(int $user_id = null, int $lead_id = null, string $command = null, array $data = [], bool $multiple = true) : bool
    {
        $socket = 'tcp://127.0.0.1:1234';
        if($command) {
            $data['command'] = $command;
        }
        $jsonData = [];

        if($user_id) {
            $jsonData['user_id'] = $user_id;
        }

        if($lead_id) {
            $jsonData['lead_id'] = $lead_id;
        }

        $jsonData['multiple'] = $multiple;
        $jsonData['data'] = $data;

        try {
            // connect tcp-server
            $instance = stream_socket_client($socket);
            // send message
            if (fwrite($instance, Json::encode($jsonData) . "\n")) {
                return true;
            }
        } catch (\Throwable $exception) {
            Yii::error(VarDumper::dumpAsString($exception->getMessage(), 10), 'Notifications:socket:stream_socket_client');
        }
        return false;
    }


    /**
     *
     */
    public static function pingUserMap(): void
    {
        $users = UserConnection::find()
            ->select('uc_user_id')
            ->andWhere(['uc_controller_id' => 'call', 'uc_action_id' => 'user-map'])
            ->groupBy(['uc_user_id'])
            ->cache(60)
            ->column();

        if($users) {
            foreach ($users as $user_id) {
                self::socket($user_id, null, 'callMapUpdate', [], true);
            }
        }
    }

}

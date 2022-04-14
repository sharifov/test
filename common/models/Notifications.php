<?php

namespace common\models;

use common\components\jobs\TelegramSendMessageJob;
use common\components\purifier\Purifier;
use common\components\purifier\PurifierFilter;
use common\models\query\NotificationsQuery;
use frontend\widgets\notification\NotificationCache;
use frontend\widgets\notification\NotificationMessage;
use src\helpers\app\AppHelper;
use src\helpers\NotificationsHelper;
use src\services\telegram\TelegramService;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Json;

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
 * @property array $_eventList
 *
 * @property Employee $nUser
 * @property string $typeIcon
 */
class Notifications extends ActiveRecord
{
    public const TYPE_SUCCESS = 1;
    public const TYPE_INFO = 2;
    public const TYPE_WARNING = 3;
    public const TYPE_DANGER = 4;

    private $_eventList = [];

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
            'n_type_id' => 'Type',
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
        return new NotificationsQuery(static::class);
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
        if (!$type_id) {
            $type_id = $this->n_type_id;
        }

        $list = self::getTypeList();
        if (isset($list[$type_id])) {
            return $list[$type_id];
        } else {
            return '???';
        }
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
        $list = self::find()->where(['n_user_id' => $user_id, 'n_new' => true, 'n_deleted' => false])->limit(10)->orderBy(['n_id' => SORT_DESC])->all();
        return $list;
    }

    /**
     * @return string
     */
    public function getNotifyType()
    {
        switch ($this->n_type_id) {
            case self::TYPE_SUCCESS:
                $str = 'success';
                break;
            case self::TYPE_INFO:
                $str = 'info';
                break;
            case self::TYPE_WARNING:
                $str = 'notice';
                break;
            case self::TYPE_DANGER:
                $str = 'error';
                break;
            default:
                $str = 'info';
        }
        return $str;
    }

    public function isMustPopupShow(): bool
    {
        return $this->n_popup && !$this->n_popup_show;
    }

    /**
     * @param int $user_id
     * @param string $title
     * @param string $message
     * @param int $type
     * @param bool $popup
     * @param bool $unique
     * @return self|null
     */
    public static function create($user_id = 0, $title = '', $message = '', $type = 1, $popup = true, $unique = false): ?self
    {
        $md5Hash = md5($message . $user_id);
        if ($unique) {
            $exists = self::find()->where(['n_unique_id' => $md5Hash])->exists();
            if ($exists) {
                return null;
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

        if ($model->n_popup && Yii::$app->params['settings']['notification_web_socket']) {
            $model->n_popup_show = true;
        }

        if ($model->save()) {
            return $model;
        }

        return null;
    }


    public function afterSave($insert, $changedAttributes)
    {
        NotificationCache::invalidate($this->n_user_id);

        parent::afterSave($insert, $changedAttributes);

        if ($insert && TelegramService::getTelegramChatIdByUserId($this->n_user_id)) {
            $job = new TelegramSendMessageJob();
            $job->user_id = $this->n_user_id;
            $job->text = Purifier::purify($this->n_message, PurifierFilter::shortCodeToIdUrl());

            $queue = Yii::$app->queue_job;
            $jobId = $queue->push($job);
        }
    }


//    /**
//     * @param int|null $user_id
//     * @param int|null $lead_id
//     * @param string|null $command
//     * @param array $data
//     * @param bool $multiple
//     * @return bool
//     */
//    public static function socket(int $user_id = null, int $lead_id = null, string $command = null, array $data = [], bool $multiple = true) : bool
//    {
//        $socket = 'tcp://127.0.0.1:1234';
//        if($command) {
//            $data['command'] = $command;
//        }
//        $jsonData = [];
//
//        if($user_id) {
//            $jsonData['user_id'] = $user_id;
//        }
//
//        if($lead_id) {
//            $jsonData['lead_id'] = $lead_id;
//        }
//
//        $jsonData['multiple'] = $multiple;
//        $jsonData['data'] = $data;
//
//        try {
//            // connect tcp-server
//            $instance = stream_socket_client($socket);
//            // send message
//            if (fwrite($instance, Json::encode($jsonData) . "\n")) {
//                return true;
//            }
//        } catch (\Throwable $exception) {
//            Yii::error(VarDumper::dumpAsString($exception->getMessage(), 10), 'Notifications:socket:stream_socket_client');
//        }
//        return false;
//    }


//    /**
//     * @param string $command
//     * @param array $params
//     * @param array $data
//     * @param bool $multiple
//     * @return bool
//     */
//    public static function sendSocket(string $command, array $params = [], array $data = [], bool $multiple = true) : bool
//    {
//        $socket = 'tcp://127.0.0.1:1234';
//        if($command) {
//            $data['command'] = $command;
//        }
//        $jsonData = [];
//
//        if (isset($params['user_id'])) {
//            $jsonData['user_id'] = $params['user_id'];
//        }
//
//        if(isset($params['lead_id'])) {
//            $jsonData['lead_id'] = $params['lead_id'];
//        }
//
//        if(isset($params['case_id'])) {
//            $jsonData['case_id'] = $params['case_id'];
//        }
//
//        $jsonData['multiple'] = $multiple;
//        $jsonData['data'] = $data;
//
//        try {
//            // connect tcp-server
//            $instance = stream_socket_client($socket);
//            // send message
//            if (fwrite($instance, Json::encode($jsonData) . "\n")) {
//                return true;
//            }
//        } catch (\Throwable $exception) {
//            Yii::error(VarDumper::dumpAsString($exception->getMessage(), 10), 'Notifications:socket:stream_socket_client');
//        }
//        return false;
//    }

    /**
     * @param string $command
     * @param array $params
     * @param array $data
     * @return bool
     */
    public static function publish(string $command, array $params = [], array $data = []): bool
    {
        $channels = [];

        if ($command) {
            $data['cmd'] = $command;
        }

        if (!empty($params['user_id'])) {
            $channels[] = 'user-' . (int) $params['user_id'];
        }

        if (!empty($params['lead_id'])) {
            $channels[] = 'lead-' . (int) $params['lead_id'];
        }

        if (!empty($params['case_id'])) {
            $channels[] = 'case-' . (int) $params['case_id'];
        }

        //$jsonData['multiple'] = $multiple;
        $jsonData = $data;

//        if (!empty($params['user_id']) && $params['user_id'] == 843) {
//            $errorData = [];
//            $errorData['command'] = $command;
//            $errorData['params'] = $params;
//            $errorData['data'] = $data;
//            $errorData['channels'] = $channels;
//            //$errorData['jsonData'] = $jsonData;
//            Yii::info($errorData, 'info\Notifications:publish');
//        }

        try {
            $redis = \Yii::$app->redis;
            if ($channels) {
                $jsonDataEncode = Json::encode($jsonData, JSON_INVALID_UTF8_SUBSTITUTE); // , JSON_UNESCAPED_UNICODE
                foreach ($channels as $channel) {
                    $redis->publish($channel, $jsonDataEncode);
                }
                return true;
            }
        } catch (\Throwable $throwable) {
            $errorData = AppHelper::throwableLog($throwable);
            $errorData['command'] = $command;
            $errorData['channels'] = $channels;
            $errorData['params'] = $params;
            $errorData['data'] = $data;
//            $errorData['jsonData'] = $jsonData;
            \Yii::error(AppHelper::throwableLog($throwable), 'Notifications:publish:Throwable');
            \Yii::error($errorData, 'Notifications:publish:Throwable2');
//            if (!empty($params['user_id']) && $params['user_id'] == 843) {
//                Yii::info($errorData, 'info\Notifications:publish:843');
//            }
        }
        return false;
    }

    /**
     * @param array $channels
     * @param string $command
     * @param array $data
     * @return bool
     */
    public static function pub(array $channels, string $command, array $data = []): bool
    {
        $redis = \Yii::$app->redis;
        if ($command) {
            $data['cmd'] = $command;
        }
        $jsonData = $data;
        try {
            if ($channels) {
                foreach ($channels as $channel) {
                    $redis->publish($channel, Json::encode($jsonData));
                }
                return true;
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable), 'Notifications:pub:redis');
        }
        return false;
    }


    /**
     *
     */
    public static function pingUserMap(): void
    {
//        $user = UserConnection::find()
//            ->select('uc_user_id')
//            ->andWhere(['uc_controller_id' => 'call', 'uc_action_id' => 'user-map'])
//            ->groupBy(['uc_user_id'])
//            ->cache(60)
//            ->column();

        $userConnections = UserConnection::find()
            ->select('uc_id')
            ->andWhere(['uc_controller_id' => 'call', 'uc_action_id' => 'user-map'])
            ->orWhere(['uc_controller_id' => 'call', 'uc_action_id' => 'realtime-user-map'])
//            ->cache(60)
            ->column();

        if ($userConnections) {
            $pubChannelList = [];
            foreach ($userConnections as $uc_id) {
                // self::socket($user_id, null, 'callMapUpdate', [], true);
                // self::publish('callMapUpdate', ['user_id' => $user_id]);
                $pubChannelList[] = 'con-' . $uc_id;
                // Notifications::pub([$pubChannel], 'callMapUpdate', ['uc_id' => $uc_id]);
            }
            if ($pubChannelList) {
                self::pub($pubChannelList, 'callMapUpdate', ['cnt' => count($pubChannelList)]);
            }
        }
    }

    public function changeTitle(string $title): void
    {
        $this->n_title = $title;
    }


    /**
     * @return array
     */
    public function triggerEvents(): array
    {
        if ($this->_eventList) {
            foreach ($this->_eventList as $eventName) {
                $this->trigger($eventName);
            }
        }
        return $this->_eventList;
    }

    /**
     * Attaches an event handler to an event.
     *
     * @param string $name the event name
     * @param callable $handler the event handler
     * @param mixed $data the data to be passed to the event handler when the event is triggered.
     * When the event handler is invoked, this data can be accessed via [[Event::data]].
     * @param bool $append whether to append new event handler to the end of the existing
     * handler list. If false, the new handler will be inserted at the beginning of the existing
     * handler list.
     */
    public function addEvent($name, $handler, $data = null, $append = true): void
    {
        $this->on($name, $handler, $data, $append);
        $this->_eventList[] = $name;
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
    public static function createAndPublish($user_id = 0, $title = '', $message = '', $type = 1, $popup = true, $unique = false): bool
    {
        if ($ntf = self::create($user_id, $title, $message, $type, $popup, $unique)) {
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
            return self::publish('getNewNotification', ['user_id' => $user_id], $dataNotification);
        }
        return false;
    }

    public static function sendCommandByControllerAction(
        string $command,
        string $controller,
        string $action,
        array $data = [],
        bool $isOnline = true,
        bool $idleOnline = false
    ): array {
        $resultUserIds = [];
        if ($userIds = UserConnection::getUsersByControllerAction($controller, $action, $isOnline, $idleOnline)) {
            foreach ($userIds as $userId) {
                $socketParams['user_id'] = $userId;
                self::publish($command, $socketParams, ['data' => $data]);
                $resultUserIds[] = $userId;
            }
        }
        return $resultUserIds;
    }

    public static function sendCommandByChanel(
        string $command,
        int $channelId,
        array $data = [],
        string $controller = 'client-chat',
        string $action = 'index',
        bool $isOnline = true,
        bool $idleOnline = false
    ): array {
        $resultUserIds = [];
        if ($userIds = UserConnection::getUsersByChanel($channelId, $controller, $action, $isOnline, $idleOnline)) {
            foreach ($userIds as $userId) {
                $socketParams['user_id'] = $userId;
                self::publish($command, $socketParams, ['data' => $data]);
                $resultUserIds[] = $userId;
            }
        }
        return $resultUserIds;
    }

    public function getTypeIcon(): string
    {
        return NotificationsHelper::getIcon($this->n_type_id);
    }
}

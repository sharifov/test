<?php

namespace common\models;

use common\models\query\UserConnectionQuery;
use sales\entities\cases\Cases;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_connection".
 *
 * @property int $uc_id
 * @property int $uc_connection_id
 * @property int|null $uc_user_id
 * @property int|null $uc_lead_id
 * @property string|null $uc_user_agent
 * @property string|null $uc_controller_id
 * @property string|null $uc_action_id
 * @property string|null $uc_page_url
 * @property string|null $uc_ip
 * @property string|null $uc_created_dt
 * @property int|null $uc_case_id
 * @property string|null $uc_connection_uid
 * @property string|null $uc_app_instance
 * @property string|null $uc_sub_list
 * @property int|null $uc_window_state
 * @property string|null $uc_window_state_dt
 * @property int|null $uc_idle_state
 * @property string|null $uc_idle_state_dt
 *
 * @property Cases $ucCase
 * @property Lead $ucLead
 * @property Employee $ucUser
 */
class UserConnection extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_connection';
    }

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['uc_connection_id'], 'required'],
            [['uc_connection_id', 'uc_user_id', 'uc_lead_id', 'uc_case_id'], 'integer'],
            [['uc_window_state', 'uc_idle_state'], 'boolean'],
            [['uc_connection_uid'], 'string', 'max' => 30],
            [['uc_connection_uid'], 'unique'],
            [['uc_user_agent', 'uc_sub_list'], 'string', 'max' => 255],
            [['uc_controller_id', 'uc_action_id'], 'string', 'max' => 50],
            [['uc_page_url'], 'string', 'max' => 500],
            [['uc_ip'], 'string', 'max' => 40],
            [['uc_app_instance'], 'string', 'max' => 20],
            [['uc_created_dt', 'uc_window_state_dt', 'uc_idle_state_dt'], 'safe'],
            [['uc_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['uc_case_id' => 'cs_id']],
            [['uc_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['uc_lead_id' => 'id']],
            [['uc_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uc_user_id' => 'id']],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'uc_id' => 'ID',
            'uc_connection_id' => 'Connection ID',
            'uc_user_id' => 'User ID',
            'uc_lead_id' => 'Lead ID',
            'uc_user_agent' => 'User Agent',
            'uc_controller_id' => 'Controller',
            'uc_action_id' => 'Action',
            'uc_page_url' => 'Page Url',
            'uc_ip' => 'IP',
            'uc_created_dt' => 'Created Dt',
            'uc_case_id' => 'Case ID',
            'uc_connection_uid' => 'Connection UID',
            'uc_app_instance' => 'App Instance',
            'uc_sub_list' => 'Subscribe List',
            'uc_window_state' => 'Window State',
            'uc_window_state_dt' => 'Window State Dt',
            'uc_idle_state' => 'IDLE State',
            'uc_idle_state_dt' => 'IDLE State Dt',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['uc_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUcCase(): ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'uc_case_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUcLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'uc_lead_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUcUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uc_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserConnectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserConnectionQuery(static::class);
    }

//    /**
//     * @param bool $insert
//     * @param array $changedAttributes
//     */
//    public function afterSave($insert, $changedAttributes)
//    {
//        parent::afterSave($insert, $changedAttributes);
//
//        if ($insert) {
//            //NativeEventDispatcher::recordEvent(UserConnectionEvents::class, UserConnectionEvents::INSERT, [UserConnectionEvents::class, 'insertUserOnline'], $this);
//            //NativeEventDispatcher::trigger(UserConnectionEvents::class, UserConnectionEvents::INSERT);
//
//            if ($this->uc_user_id) {
//                $exist = UserOnline::find()->where(['uo_user_id' => $this->uc_user_id])->exists();
//
//                if (!$exist) {
//                    $uo = new UserOnline();
//                    $uo->uo_user_id = $this->uc_user_id;
//                    if (!$uo->save()) {
//                        \Yii::error(VarDumper::dumpAsString($uo->errors), 'UserConnectionEvents:insertUserOnline:UserOnline:save');
//                    }
//                }
//            }
//
//        }
//    }


//    /**
//     * @return bool
//     */
//    public function beforeDelete(): bool
//    {
//        if (!parent::beforeDelete()) {
//            return false;
//        }
//
//        NativeEventDispatcher::recordEvent(UserConnectionEvents::class, UserConnectionEvents::DELETE, [UserConnectionEvents::class, 'deleteUserOnline'], $this);
//        return true;
//    }
//
//    public function afterDelete(): void
//    {
//        parent::afterDelete();
//        NativeEventDispatcher::trigger(UserConnectionEvents::class, UserConnectionEvents::DELETE);
//    }

    /**
     * @param int $userId
     * @return array|UserConnection|null
     */
    public static function getLastUserConnection(int $userId)
    {
        return self::find()->select(['uc_id'])->where(['uc_user_id' => $userId])->orderBy(['uc_id' => SORT_DESC])->limit(1)->one();
    }

    /**
     * @param int $userId
     * @return string|null
     */
    public static function getLastUserChannel(int $userId): ?string
    {
        $uc = self::getLastUserConnection($userId);
        if ($uc && $uc->uc_id) {
            return 'con-' . $uc->uc_id;
        }
        return null;
    }

    /**
     * @param int $userId
     * @return string
     */
    public static function getUserChannel(int $userId): string
    {
        return 'user-' . $userId;
    }

    /**
     * @return bool
     */
    public static function isIdleMonitorEnabled(): bool
    {
        return \Yii::$app->params['settings']['idle_monitor_enabled'] ?? false;
    }

    /**
     * @return int
     */
    public static function idleSeconds(): int
    {
        return \Yii::$app->params['settings']['idle_seconds'] ?? 0;
    }

    public static function getUsersByControllerAction(
        string $controller,
        string $action,
        bool $isOnline = true,
        bool $idleOnline = false
    ): array {
        $query = self::find()
            ->select(['uc_user_id'])
            ->where(['uc_controller_id' => $controller])
            ->andWhere(['uc_action_id' => $action]);

        if ($isOnline) {
            $query->innerJoin(UserOnline::tableName() . ' AS user_online', 'user_online.uo_user_id = uc_user_id');
            $query->andWhere(['user_online.uo_idle_state' => $idleOnline]);
        }

        return $query->orderBy(['uc_id' => SORT_DESC])
            ->indexBy('uc_user_id')
            ->column();
    }
}

<?php

namespace sales\model\user\entity\monitor;

use common\models\Employee;
use common\models\UserConnection;
use common\models\UserOnline;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "user_monitor".
 *
 * @property int $um_id
 * @property int $um_user_id
 * @property int $um_type_id
 * @property string|null $um_start_dt
 * @property string|null $um_end_dt
 * @property int|null $um_period_sec
 * @property string|null $um_description
 *
 * @property Employee $umUser
 */
class UserMonitor extends \yii\db\ActiveRecord
{
    public const TYPE_ONLINE        = 1;
    public const TYPE_ACTIVE        = 3;
    public const TYPE_LOGIN         = 5;
    public const TYPE_LOGOUT        = 6;

    public const TYPE_LIST        = [
        self::TYPE_ONLINE   => 'Online',
        self::TYPE_ACTIVE   => 'Active',
        self::TYPE_LOGIN    => 'Login',
        self::TYPE_LOGOUT   => 'Logout',
    ];

    public const TYPE_BGCOLOR_LIST        = [
        self::TYPE_ONLINE   => '#F5F1BD',
        self::TYPE_ACTIVE   => '#89c997',
        self::TYPE_LOGIN    => '#ff0000',
        self::TYPE_LOGOUT   => '#919496',
    ];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_monitor';
    }

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['um_user_id', 'um_type_id'], 'required'],
            [['um_user_id', 'um_type_id', 'um_period_sec'], 'integer'],
            [['um_start_dt', 'um_end_dt'], 'safe'],
            [['um_description'], 'string', 'max' => 255],
            [['um_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['um_user_id' => 'id']],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'um_id' => 'ID',
            'um_user_id' => 'User ID',
            'um_type_id' => 'Type ID',
            'um_start_dt' => 'Start Dt',
            'um_end_dt' => 'End Dt',
            'um_period_sec' => 'Period Sec',
            'um_description' => 'Description',
        ];
    }

    /**
     * Gets query for [[UmUser]].
     *
     * @return ActiveQuery
     */
    public function getUmUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'um_user_id']);
    }

    /**
     * @return string[]
     */
    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return self::TYPE_LIST[$this->um_type_id] ?? '';
    }

    /**
     * @return string
     */
    public function getTypeBgColor(): string
    {
        return self::TYPE_BGCOLOR_LIST[$this->um_type_id] ?? '';
    }

    /**
     * @param int $userId
     * @param int $typeId
     * @return UserMonitor
     */
    public static function addEvent(int $userId, int $typeId): UserMonitor
    {
        $um = new self();
        $um->um_user_id = $userId;
        $um->um_type_id = $typeId;
        $um->um_start_dt = date('Y-m-d H:i:s');
        if (!$um->save()) {
            Yii::error(VarDumper::dumpAsString(['errors' => $um->errors, 'attributes' => $um->attributes]), 'UserMonitor:addEvent:save');
        }
        return $um;
    }

    /**
     * @param int $userId
     * @return false
     */
    public static function closeConnectionEvent(int $userId): bool
    {

        $exist = UserConnection::find()->where(['uc_user_id' => $userId])->exists();

        if ($exist) {
            self::updateGlobalIdle($userId);
            unset($exist);
            return false;
        }
        unset($exist);

        $lastItem = self::find()->where(['um_user_id' => $userId, 'um_type_id' => self::TYPE_ONLINE])->limit(1)->orderBy(['um_id' => SORT_DESC])->one();
        if (!$lastItem) {
            return false;
        }

        $lastItem->um_end_dt = date('Y-m-d H:i:s');
        $lastItem->um_period_sec = time() - strtotime($lastItem->um_start_dt);
        if (!$lastItem->save()) {
            Yii::error(VarDumper::dumpAsString(['errors' => $lastItem->errors, 'attributes' => $lastItem->attributes]), 'UserMonitor:closeConnectionEvent:save');
        }
        unset($lastItem);
        return true;
    }

    /**
     * @param int $userId
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public static function updateGlobalIdle(int $userId): bool
    {
        $out = false;
        $userOnline = UserOnline::find()->where(['uo_user_id' => $userId])->one();
        if ($userOnline) {
            $ucList = UserConnection::find()->select(['uc_idle_state'])->where(['uc_user_id' => $userId])->all();
            if ($ucList) {

                $idleState = true;
                foreach ($ucList as $uc) {
                    if (!$uc->uc_idle_state) {
                        $idleState = false;
                        break;
                    }
                }

                if ($userOnline->uo_idle_state !== $idleState) {
                    $userOnline->uo_idle_state = $idleState;
                    $userOnline->uo_idle_state_dt = date('Y-m-d H:i:s');
                    $userOnline->update();
                    $out = true;
                }
            }
            unset($userOnline, $ucList, $idleState);
        }
        return $out;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public static function setUserActive(int $userId): bool
    {
        //$online = UserConnection::find()->where(['uc_user_id' => $userId])->limit(1)->exists();
        $lastItem = self::find()->where(['um_user_id' => $userId, 'um_type_id' => self::TYPE_ACTIVE])->limit(1)->orderBy(['um_id' => SORT_DESC])->one();
        if (!$lastItem || $lastItem->um_end_dt) {
            self::addEvent($userId, self::TYPE_ACTIVE);
            return true;
        }
        return false;
    }

    /**
     * @param int $userId
     * @return bool
     */
    public static function setUserIdle(int $userId): bool
    {
        //$online = UserConnection::find()->where(['uc_user_id' => $userId])->limit(1)->exists();
        $lastItem = self::find()->where(['um_user_id' => $userId, 'um_type_id' => self::TYPE_ACTIVE])->limit(1)->orderBy(['um_id' => SORT_DESC])->one();
        if ($lastItem && !$lastItem->um_end_dt) {
            $lastItem->um_end_dt = date('Y-m-d H:i:s');
            $lastItem->um_period_sec = time() - strtotime($lastItem->um_start_dt);
            if (!$lastItem->save()) {
                Yii::error(VarDumper::dumpAsString(['errors' => $lastItem->errors, 'attributes' => $lastItem->attributes]), 'UserMonitor:setUserIdle:save');
            }
            return true;
            //self::addEvent($userId, self::TYPE_ACTIVE);
        }
        return false;
    }


    /**
     * @return bool
     */
    public static function isAutologoutEnabled(): bool
    {
        return \Yii::$app->params['settings']['autologout_enabled'] ?? false;
    }

    /**
     * @return int
     */
    public static function isAutologoutTimerSec(): int
    {
        return \Yii::$app->params['settings']['autologout_timer_sec'] ?? 0;
    }

    /**
     * @return bool
     */
    public static function isAutologoutShowMessage(): bool
    {
        return \Yii::$app->params['settings']['autologout_show_message'] ?? false;
    }

    /**
     * @return int
     */
    public static function autologoutIdlePeriodMin(): int
    {
        return \Yii::$app->params['settings']['autologout_idle_period_min'] ?? 0;
    }

}

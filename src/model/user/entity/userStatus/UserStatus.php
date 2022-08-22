<?php

namespace src\model\user\entity\userStatus;

use common\models\Call;
use common\models\ConferenceParticipant;
use common\models\Employee;
use common\models\Notifications;
use src\helpers\app\AppHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_status".
 *
 * @property int $us_user_id
 * @property int|null $us_gl_call_count
 * @property bool|null $us_call_phone_status
 * @property bool|null $us_is_on_call
 * @property bool|null $us_has_call_access
 * @property string|null $us_updated_dt
 *
 * @property Employee $usUser
 * @property int $us_phone_ready_time [int]
 * @property string|null $us_phone_ready_dt
 */
class UserStatus extends ActiveRecord
{
    public const CHANNEL_NAME = 'userStatusChannel';

    public string $us_phone_ready_dt = '';

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_status';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['us_gl_call_count', 'us_phone_ready_time'], 'integer'],
            [['us_call_phone_status', 'us_is_on_call', 'us_has_call_access'], 'boolean'],
            [['us_call_phone_status', 'us_is_on_call', 'us_has_call_access'], 'filter', 'filter' => 'boolval'],
            [['us_updated_dt', 'us_phone_ready_dt'], 'safe'],
            [['us_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['us_user_id' => 'id']],
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['us_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['us_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'us_user_id' => 'User ID',
            'us_gl_call_count' => 'General Line Call Count',
            'us_call_phone_status' => 'Call Phone Status',
            'us_is_on_call' => 'Is On Call',
            'us_has_call_access' => 'Has Call Access',
            'us_phone_ready_time' => 'Phone Ready Time',
            'us_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[UsUser]].
     *
     * @return ActiveQuery
     */
    public function getUsUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'us_user_id']);
    }

    public static function updateIsOnnCall(int $createdUserId, ?int $groupId): void
    {
        if (!$user = Employee::findOne($createdUserId)) {
            return;
        }

        $activeAnotherCall = Call::find()
            ->andWhere(['c_created_user_id' => $user->id, 'c_status_id' => [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING]])
            ->andWhere(['<>', 'c_group_id', $groupId])
            ->innerJoin(
                ConferenceParticipant::tableName(),
                'cp_call_id = c_id AND cp_status_id != :status AND cp_type_id = :type',
                [
                    ':status' => ConferenceParticipant::STATUS_LEAVE,
                    ':type' => ConferenceParticipant::TYPE_AGENT,
                ]
            )
            ->exists();

        if (!$activeAnotherCall && $user->userStatus) {
            $user->userStatus->us_is_on_call = false;
            if (!$user->userStatus->save()) {
                Yii::error('Cant update user status', 'UserStatus:updateIsOnnCall');
            }
        }
    }

    public static function isOnCallOn(int $userId): void
    {
        $status = self::findOne(['us_user_id' => $userId]);
        if (!$status) {
            $status = new self([
                'us_user_id' => $userId,
                'us_gl_call_count' => 0,
            ]);
        } elseif ($status->us_is_on_call) {
            return;
        }
        $status->us_is_on_call = true;
        try {
            $status->save(false);
        } catch (\Throwable $e) {
            Yii::error(['message' => 'User status is on call(on) save error', 'e' => $e->getMessage()], 'UserStatus:isOnCallOn');
        }
    }

    public static function isOnCallOff(int $userId): void
    {
        $status = self::findOne(['us_user_id' => $userId]);
        if (!$status) {
            $status = new self([
                'us_user_id' => $userId,
                'us_gl_call_count' => 0,
            ]);
        } elseif (!$status->us_is_on_call) {
            return;
        }
        $status->us_is_on_call = false;
        try {
            $status->save(false);
        } catch (\Throwable $e) {
            Yii::error(['message' => 'User status is on call(off) save error', 'e' => $e->getMessage()], 'UserStatus:isOnCallOff');
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->sendFrontendData($insert ? 'insert' : 'update');
        if ($insert || array_key_exists('us_is_on_call', $changedAttributes)) {
            if ($this->us_is_on_call) {
                Notifications::publish('hidePhoneNotifications', ['user_id' => $this->us_user_id], []);
            }
        }
    }

    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        $this->sendFrontendData('delete');
        return true;
    }

    /**
     * @param string $action
     * @return false|mixed
     */
    public function sendFrontendData(string $action = 'update')
    {
        $enabled = !empty(Yii::$app->params['centrifugo']['enabled']);
        if ($enabled) {
            try {
                return Yii::$app->centrifugo->setSafety(false)
                    ->publish(
                        self::CHANNEL_NAME,
                        [
                            'object' => 'userStatus',
                            'action' => $action,
                            'id' => $this->us_user_id,
                            'data' => [
                                'userStatus' => ArrayHelper::toArray($this, [
                                    UserStatus::class => [
                                        'user_id' => 'us_user_id',
                                        'us_user_id',
                                        'us_gl_call_count',
                                        'us_call_phone_status',
                                        'us_is_on_call',
                                        'us_has_call_access',
                                        'us_updated_dt',
                                        'userName' => function (UserStatus $us) {
                                            return $us->usUser->username ?? '';
                                        },
                                        'online' => function (UserStatus $us) {
                                            return $us->usUser->userOnline->attributes ?? [];
                                        },
                                        'status' => function (UserStatus $us) {
                                            return $us->attributes;
                                        },
                                        'userDep' => function (UserStatus $us) {
                                            $deps = [];
                                            foreach ($us->usUser->udDeps as $dep) {
                                                if (isset($dep->dep_id)) {
                                                    $deps[] = $dep->dep_id;
                                                }
                                            }
                                            return $deps;
                                        },
                                    ],
                                ]),
                            ]
                        ]
                    );
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'UserStatus:sendFrontendData:Throwable');
                return false;
            }
        }
    }

    public function updatePhoneReadyTime(): void
    {
        $this->us_phone_ready_time = time();
    }

    /**
     * @return bool
     */
    public function isOnCall(): bool
    {
        return (bool) $this->us_is_on_call;
    }
}

<?php

namespace sales\model\user\entity\userStatus;

use common\models\Call;
use common\models\Employee;
use sales\helpers\app\AppHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
 */
class UserStatus extends ActiveRecord
{
    public const CHANNEL_NAME = 'userStatusChannel';

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
            [['us_gl_call_count'], 'integer'],
            [['us_call_phone_status', 'us_is_on_call', 'us_has_call_access'], 'boolean'],
            [['us_call_phone_status', 'us_is_on_call', 'us_has_call_access'], 'filter', 'filter' => 'boolval'],
            [['us_updated_dt'], 'safe'],
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
            ->where(['c_created_user_id' => $user->id, 'c_status_id' => [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING]])
            ->andWhere(['<>', 'c_group_id', $groupId])
            ->exists();

        if (!$activeAnotherCall && $user->userStatus) {
            $user->userStatus->us_is_on_call = false;
            if (!$user->userStatus->save()) {
                Yii::error('Cant update user status', 'UserStatus:updateIsOnnCall');
            }
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
                                'userStatus' => $this->attributes,
                            ]
                        ]
                    );
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'UserStatus:sendFrontendData:Throwable');
                return false;
            }
        }
    }
}

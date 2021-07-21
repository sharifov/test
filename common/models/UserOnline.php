<?php

namespace common\models;

use common\models\query\UserOnlineQuery;
use sales\helpers\app\AppHelper;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "user_online".
 *
 * @property int $uo_user_id
 * @property string|null $uo_updated_dt
 * @property int|null $uo_idle_state
 * @property string|null $uo_idle_state_dt
 *
 * @property Employee $uoUser
 */
class UserOnline extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_online';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['uo_updated_dt', 'uo_idle_state_dt'], 'safe'],
            [['uo_idle_state'], 'boolean'],
            [['uo_idle_state'], 'filter', 'filter' => 'boolval'],
            [['uo_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uo_user_id' => 'id']],
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['uo_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['uo_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'uo_user_id' => 'User ID',
            'uo_updated_dt' => 'Updated Dt',
            'uo_idle_state' => 'Idle State',
            'uo_idle_state_dt' => 'Idle State Dt',
        ];
    }

    /**
     * Gets query for [[UoUser]].
     *
     * @return ActiveQuery
     */
    public function getUoUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uo_user_id']);
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
                        Call::CHANNEL_USER_ONLINE,
                        [
                            'object' => 'userOnline',
                            'action' => $action,
                            'id' => $this->uo_user_id,
                            'data' => [
                                'userOnline' => $this->attributes,
                            ]
                        ]
                    );
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'UserOnline:sendFrontendData:Throwable');
                return false;
            }
        }
    }

    public static function find(): UserOnlineQuery
    {
        return new UserOnlineQuery(static::class);
    }
}

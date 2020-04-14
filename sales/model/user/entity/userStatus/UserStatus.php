<?php

namespace sales\model\user\entity\userStatus;

use common\models\Call;
use common\models\Employee;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

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

    public static function updateIsOnnCall(Call $call): void
    {
        $onCallWithAnotherCall = Call::find()->
            where(['c_created_user_id' => $call->c_created_user_id, 'c_status_id' => [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING]])
            ->andWhere(['<>', 'c_group_id', $call->c_group_id])
            ->exists();
        Yii::info(VarDumper::dumpAsString([
            'models' => Call::find()->
                                where(['c_created_user_id' => $call->c_created_user_id, 'c_status_id' => [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING]])
                                ->andWhere(['<>', 'c_group_id', $call->c_group_id])
                                ->asArray()->all(),
            'query' => Call::find()
                                ->where(['c_created_user_id' => $call->c_created_user_id, 'c_status_id' => [Call::STATUS_IN_PROGRESS, Call::STATUS_RINGING]])
                                ->andWhere(['<>', 'c_group_id', $call->c_group_id])->createCommand()->getRawSql()
        ]), 'info\DebugCallRedirect');
        if (!$onCallWithAnotherCall && isset($call->cCreatedUser->userStatus)) {
            $call->cCreatedUser->userStatus->us_is_on_call = false;
            if (!$call->cCreatedUser->userStatus->save()) {
                Yii::error('Cant update user status', 'UserStatus:updateIsOnnCall');
            }
        }
    }
}

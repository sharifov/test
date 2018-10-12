<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_params".
 *
 * @property int $up_user_id
 * @property int $up_commission_percent
 * @property string $up_base_amount
 * @property string $up_updated_dt
 * @property int $up_updated_user_id
 *
 * @property Employee $upUpdatedUser
 * @property Employee $upUser
 */
class UserParams extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_params';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['up_user_id'], 'required'],
            [['up_user_id', 'up_commission_percent', 'up_updated_user_id'], 'integer'],
            [['up_base_amount'], 'number'],
            [['up_updated_dt'], 'safe'],
            [['up_user_id'], 'unique'],
            [['up_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['up_updated_user_id' => 'id']],
            [['up_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['up_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'up_user_id' => 'User ID',
            'up_commission_percent' => 'Commission Percent',
            'up_base_amount' => 'Base Amount',
            'up_updated_dt' => 'Updated Dt',
            'up_updated_user_id' => 'Updated User ID',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['up_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['up_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'up_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'up_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserParamsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserParamsQuery(get_called_class());
    }
}

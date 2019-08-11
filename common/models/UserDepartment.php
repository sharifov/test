<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_department".
 *
 * @property int $ud_user_id
 * @property int $ud_dep_id
 * @property string $ud_updated_dt
 *
 * @property Department $udDep
 * @property Employee $udUser
 */
class UserDepartment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ud_user_id', 'ud_dep_id'], 'required'],
            [['ud_user_id', 'ud_dep_id'], 'integer'],
            [['ud_updated_dt'], 'safe'],
            [['ud_user_id', 'ud_dep_id'], 'unique', 'targetAttribute' => ['ud_user_id', 'ud_dep_id']],
            [['ud_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['ud_dep_id' => 'dep_id']],
            [['ud_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ud_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ud_user_id' => 'User ID',
            'ud_dep_id' => 'Dep ID',
            'ud_updated_dt' => 'Updated Dt',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ud_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ud_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUdDep()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'ud_dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUdUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ud_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserDepartmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserDepartmentQuery(get_called_class());
    }
}

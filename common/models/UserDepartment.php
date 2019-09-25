<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

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
class UserDepartment extends ActiveRecord
{

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%user_department}}';
    }

    /**
     * @return array
     */
    public function rules(): array
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
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'ud_user_id' => 'User ID',
            'ud_dep_id' => 'Dep ID',
            'ud_updated_dt' => 'Updated Dt',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ud_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ud_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUdDep(): ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 'ud_dep_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUdUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ud_user_id']);
    }

    /**
     * @return UserDepartmentQuery
     */
    public static function find(): UserDepartmentQuery
    {
        return new UserDepartmentQuery(get_called_class());
    }
}

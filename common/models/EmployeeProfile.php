<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "employee_profile".
 *
 * @property int $id
 * @property int $employee_id
 * @property string $role
 * @property string $profile_info
 * @property string $updated_at
 *
 * @property Employee $employee
 */
class EmployeeProfile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id'], 'integer'],
            [['role', 'profile_info', 'updated_at'], 'required'],
            [['profile_info'], 'string'],
            [['updated_at'], 'safe'],
            [['role'], 'string', 'max' => 255],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Employee ID',
            'role' => 'Role',
            'profile_info' => 'Profile Info',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }
}

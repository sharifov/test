<?php

namespace frontend\models;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use Yii;

/**
 * This is the model class for table "user_failed_login".
 *
 * @property int $ufl_id
 * @property string|null $ufl_username
 * @property int|null $ufl_user_id
 * @property string|null $ufl_ua
 * @property string|null $ufl_ip
 * @property string|null $ufl_session_id
 * @property string|null $ufl_created_dt
 *
 * @property Employee $uflUser
 */
class UserFailedLogin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_failed_login';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ufl_user_id'], 'integer'],
            [['ufl_created_dt'], 'safe'],
            [['ufl_username'], 'string', 'max' => 150],
            [['ufl_ua'], 'string', 'max' => 200],
            [['ufl_ip'], 'string', 'max' => 40],
            [['ufl_session_id'], 'string', 'max' => 100],
            [['ufl_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['ufl_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ufl_id' => 'Ufl ID',
            'ufl_username' => 'Ufl Username',
            'ufl_user_id' => 'Ufl User ID',
            'ufl_ua' => 'Ufl Ua',
            'ufl_ip' => 'Ufl Ip',
            'ufl_session_id' => 'Ufl Session ID',
            'ufl_created_dt' => 'Ufl Created Dt',
        ];
    }

    /**
     * Gets query for [[UflUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUflUser()
    {
        return $this->hasOne(Employee::className(), ['id' => 'ufl_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserFailedLoginQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserFailedLoginQuery(get_called_class());
    }
}

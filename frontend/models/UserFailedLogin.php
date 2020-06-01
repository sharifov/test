<?php

namespace frontend\models;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
 * @property bool $ufl_active
 *
 * @property Employee $user
 */
class UserFailedLogin extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%user_failed_login}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['ufl_ip', 'ufl_active'], 'required'],
            [['ufl_user_id'], 'integer'],
            [['ufl_created_dt'], 'safe'],
            [['ufl_username'], 'string', 'max' => 150],
            [['ufl_ua'], 'string', 'max' => 200],
            [['ufl_ip'], 'string', 'max' => 40],
            [['ufl_session_id'], 'string', 'max' => 100],
            [['ufl_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ufl_user_id' => 'id']],
            ['ufl_active', 'boolean'],
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'ufl_id' => 'ID',
            'ufl_username' => 'Username',
            'ufl_user_id' => 'User ID',
            'ufl_ua' => 'User Agent',
            'ufl_ip' => 'Ip',
            'ufl_session_id' => 'Session ID',
            'ufl_created_dt' => 'Created',
            'ufl_active' => 'Active',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ufl_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ufl_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserFailedLoginQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserFailedLoginQuery(get_called_class());
    }

    /**
     * @param string $username
     * @param int|null $userId
     * @param string|null $userAgent
     * @param string $ip
     * @param string|null $sessionId
     * @param bool $active
     * @return static
     */
    public static function create(
        string $username,
        ?int $userId,
        ?string $userAgent,
        string $ip,
        ?string $sessionId,
        bool $active = true
    ): self {
        $model = new static();
        $model->ufl_username = $username;
        $model->ufl_user_id = $userId;
        $model->ufl_ua = $userAgent;
        $model->ufl_ip = $ip;
        $model->ufl_session_id = $sessionId;
        $model->ufl_active = $active;
        return $model;
    }

    /**
     * @param string $ip
     * @return array|UserFailedLogin[]
     */
    public static function getActiveByIp(string $ip): array
    {
        return self::find()->where(['ufl_ip' => $ip, 'ufl_active' => true])->all();
    }

    /**
     * @param string $ip
     * @return int|null
     */
    public static function getCountActiveByIp(string $ip): ?int
    {
        return self::find()->where(['ufl_ip' => $ip, 'ufl_active' => true])->count();
    }

    /**
     * @param int $userId
     * @return int|null
     */
    public static function getCountActiveByUserId(int $userId): ?int
    {
        return self::find()->where(['ufl_user_id' => $userId, 'ufl_active' => true])->count();
    }

    /**
     * @param int $userId
     * @param int $limit
     * @return array|UserFailedLogin[]
     */
    public static function getLastAttempts(int $userId, int $limit = 3): array
    {
        return self::find()
            ->where(['ufl_user_id' => $userId, 'ufl_active' => true])
            ->orderBy(['ufl_id' => SORT_DESC])
            ->limit($limit)
            ->all();
    }
}

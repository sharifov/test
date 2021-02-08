<?php

namespace sales\model\userClientChatData\entity;

use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_client_chat_data".
 *
 * @property int $uccd_id
 * @property int $uccd_employee_id
 * @property bool $uccd_active
 * @property string|null $uccd_created_dt
 * @property string|null $uccd_updated_dt
 * @property int|null $uccd_created_user_id
 * @property int|null $uccd_updated_user_id
 * @property string $uccd_auth_token
 * @property string $uccd_rc_user_id
 * @property string $uccd_password
 * @property string|null $uccd_token_expired
 * @property string $uccd_username
 * @property string $uccd_name
 *
 * @property Employee $uccdEmployee
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class UserClientChatData extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'user_client_chat_data';
    }

    public function rules(): array
    {
        return [
            [[
                'uccd_employee_id',
                'uccd_username', 'uccd_name', 'uccd_password',
            ], 'required'],

            ['uccd_employee_id', 'integer'],
            ['uccd_employee_id', 'unique'],
            ['uccd_employee_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uccd_employee_id' => 'id']],

            ['uccd_active', 'boolean'],
            ['uccd_active', 'default', 'value' => true],

            [['uccd_created_user_id', 'uccd_updated_user_id'], 'integer'],
            [['uccd_created_user_id', 'uccd_updated_user_id'],
                'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uccd_employee_id' => 'id']],

            [['uccd_created_dt', 'uccd_updated_dt'], 'date', 'format' => 'php:Y-m-d H:i:s'],

            [['uccd_auth_token', 'uccd_rc_user_id', 'uccd_password', 'uccd_username', 'uccd_name'], 'trim'],

            [['uccd_auth_token'], 'string', 'max' => 50],
            [['uccd_rc_user_id'], 'string', 'max' => 20],
            [['uccd_password'], 'string', 'min' => 8, 'max' => 50],

            [['uccd_token_expired'], 'date', 'format' => 'php:Y-m-d H:i:s'],

            [['uccd_name'], 'string', 'max' => 255],
            [['uccd_username'], 'string', 'min' => 3, 'max' => 50],
            [['uccd_username', 'uccd_name'], 'unique'],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['uccd_created_dt', 'uccd_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['uccd_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'uccd_created_user_id',
                'updatedByAttribute' => 'uccd_updated_user_id',
            ],
        ];
    }

    public function getUccdEmployee(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uccd_employee_id']);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uccd_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'uccd_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'uccd_id' => 'ID',
            'uccd_employee_id' => 'Employee',
            'uccd_created_dt' => 'Created Dt',
            'uccd_updated_dt' => 'Updated Dt',
            'uccd_created_user_id' => 'Created User',
            'uccd_updated_user_id' => 'Updated User',
            'uccd_active' => 'Active',
            'uccd_auth_token' => 'Auth token',
            'uccd_rc_user_id' => 'RC User Id',
            'uccd_password' => 'Password',
            'uccd_token_expired' => 'Token expired',
            'uccd_username' => 'Username',
            'uccd_name' => 'Name',
        ];
    }

    public static function find(): UserClientChatDataScopes
    {
        return new UserClientChatDataScopes(static::class);
    }

    public static function getOrCreateByEmployeeId(int $employeeId): UserClientChatData
    {
        if ($model = self::findOne(['uccd_employee_id' => $employeeId])) {
            return $model;
        }
        $model = new self();
        $model->uccd_employee_id = $employeeId;
        return $model;
    }

    public function isActive(): bool
    {
        return (bool) $this->uccd_active;
    }

    public function isRegisteredInRc(): bool
    {
        return $this->uccd_auth_token && $this->uccd_rc_user_id;
    }

    public function getEmployeeId(): ?int
    {
        return $this->uccd_employee_id;
    }

    public function getRcUserId(): ?string
    {
        return $this->uccd_rc_user_id;
    }

    public function getAuthToken(): ?string
    {
        return $this->uccd_auth_token;
    }
}

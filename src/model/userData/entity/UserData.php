<?php

namespace src\model\userData\entity;

use common\models\Employee;

/**
 * This is the model class for table "{{%user_data}}".
 *
 * @property int $ud_user_id
 * @property int $ud_key
 * @property string $ud_value
 * @property string|null $ud_updated_dt
 *
 * @property Employee $user
 */
class UserData extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['ud_user_id', 'ud_key'], 'unique', 'targetAttribute' => ['ud_user_id', 'ud_key']],

            ['ud_key', 'required'],
            ['ud_key', 'integer'],
            ['ud_key', 'in', 'range' => array_keys(UserDataKey::getList())],

            ['ud_user_id', 'required'],
            ['ud_user_id', 'integer'],
            ['ud_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ud_user_id' => 'id']],

            ['ud_value', 'string', 'max' => 20],

            ['ud_updated_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ud_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ud_user_id' => 'User',
            'ud_key' => 'Key',
            'ud_value' => 'Value',
            'ud_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%user_data}}';
    }
}

<?php

namespace sales\model\callNote\entity;

use common\models\Call;
use common\models\Employee;
use Yii;

/**
 * This is the model class for table "call_note".
 *
 * @property int $cn_id
 * @property int|null $cn_call_id
 * @property string|null $cn_note
 * @property string|null $cn_created_dt
 * @property string|null $cn_updated_dt
 * @property int|null $cn_created_user_id
 * @property int|null $cn_updated_user_id
 *
 * @property Call $cnCall
 * @property Employee $cnCreatedUser
 * @property Employee $cnUpdatedUser
 */
class CallNote extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cn_call_id', 'integer'],
            ['cn_call_id', 'exist', 'skipOnError' => true, 'targetClass' => Call::class, 'targetAttribute' => ['cn_call_id' => 'c_id']],

            ['cn_created_dt', 'safe'],

            ['cn_created_user_id', 'integer'],
            ['cn_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cn_created_user_id' => 'id']],

            ['cn_note', 'string', 'max' => 255],

            ['cn_updated_dt', 'safe'],

            ['cn_updated_user_id', 'integer'],
            ['cn_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cn_updated_user_id' => 'id']],
        ];
    }

    public function getCnCall(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Call::class, ['c_id' => 'cn_call_id']);
    }

    public function getCnCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cn_created_user_id']);
    }

    public function getCnUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cn_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cn_id' => 'ID',
            'cn_call_id' => 'Call ID',
            'cn_note' => 'Note',
            'cn_created_dt' => 'Created Dt',
            'cn_updated_dt' => 'Updated Dt',
            'cn_created_user_id' => 'Created User ID',
            'cn_updated_user_id' => 'Updated User ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'call_note';
    }
}

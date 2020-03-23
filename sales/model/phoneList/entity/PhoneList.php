<?php

namespace sales\model\phoneList\entity;

use common\models\Employee;
use sales\yii\validators\PhoneValidator;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%phone_list}}".
 *
 * @property int $pl_id
 * @property string $pl_phone_number
 * @property string|null $pl_title
 * @property int $pl_enabled
 * @property int|null $pl_created_user_id
 * @property int|null $pl_updated_user_id
 * @property string|null $pl_created_dt
 * @property string|null $pl_updated_dt
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class PhoneList extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%phone_list}}';
    }

    public function rules(): array
    {
        return [
            ['pl_phone_number', PhoneValidator::class, 'required' => true],
            ['pl_phone_number', 'unique'],

            ['pl_title', 'string', 'max' => 50],

            ['pl_enabled', 'required'],
            ['pl_enabled', 'boolean'],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pl_created_dt', 'pl_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'attribute' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pl_created_user_id', 'pl_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pl_updated_user_id'],
                ],
                'value' => \Yii::$app->user->id ?? null,
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'pl_id' => 'ID',
            'pl_phone_number' => 'Phone Number',
            'pl_title' => 'Title',
            'pl_enabled' => 'Enabled',
            'pl_created_user_id' => 'Created User',
            'pl_updated_user_id' => 'Updated User',
            'pl_created_dt' => 'Created Dt',
            'pl_updated_dt' => 'Updated Dt',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pl_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pl_updated_user_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}

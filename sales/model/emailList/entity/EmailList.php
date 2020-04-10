<?php

namespace sales\model\emailList\entity;

use common\models\Employee;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%email_list}}".
 *
 * @property int $el_id
 * @property string $el_email
 * @property string|null $el_title
 * @property int $el_enabled
 * @property int|null $el_created_user_id
 * @property int|null $el_updated_user_id
 * @property string|null $el_created_dt
 * @property string|null $el_updated_dt
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class EmailList extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%email_list}}';
    }

    public function rules(): array
    {
        return [
            ['el_email', 'required'],
            ['el_email', 'email'],
            ['el_email', 'string', 'max' => 160],
            ['el_email', 'unique'],

            ['el_title', 'string', 'max' => 50],

            ['el_enabled', 'required'],
            ['el_enabled', 'boolean'],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['el_created_dt', 'el_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['el_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'attribute' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['el_created_user_id', 'el_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['el_updated_user_id'],
                ],
                'value' => \Yii::$app->user->id ?? null,
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'el_id' => 'ID',
            'el_email' => 'Email',
            'el_title' => 'Title',
            'el_enabled' => 'Enabled',
            'el_created_user_id' => 'Created User',
            'el_updated_user_id' => 'Updated User',
            'el_created_dt' => 'Created Dt',
            'el_updated_dt' => 'Updated Dt',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'el_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'el_updated_user_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}

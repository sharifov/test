<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_blacklist".
 *
 * @property int $pbl_id
 * @property string $pbl_phone
 * @property string|null $pbl_description
 * @property int|null $pbl_enabled
 * @property string|null $pbl_created_dt
 * @property string|null $pbl_updated_dt
 * @property int|null $pbl_updated_user_id
 * @property string|null $pbl_expiration_date
 *
 * @property Employee $updatedUser
 */
class PhoneBlacklist extends \yii\db\ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pbl_created_dt', 'pbl_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pbl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'attribute' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pbl_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pbl_updated_user_id'],
                ],
                'value' => isset(\Yii::$app->user) ? \Yii::$app->user->id : null,
            ],
        ];
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'phone_blacklist';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['pbl_phone', 'required'],
            ['pbl_phone', 'string', 'max' => 30],
            ['pbl_phone', 'unique'],
            ['pbl_phone', 'filter', 'filter' => static function($value) {
                return $value === null ? null : str_replace(['-', ' '], '', trim($value));
            }, 'skipOnError' => true],
            ['pbl_phone', 'match', 'pattern' => '/^\+[0-9]+$/', 'message' => 'The format of {attribute} is invalid.'],

            ['pbl_enabled', 'boolean'],
            ['pbl_expiration_date', 'date', 'format' => 'php:Y-m-d'],
            ['pbl_description', 'string', 'max' => 255],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'pbl_id' => 'ID',
            'pbl_phone' => 'Phone',
            'pbl_description' => 'Description',
            'pbl_enabled' => 'Enabled',
            'pbl_created_dt' => 'Created',
            'pbl_updated_dt' => 'Updated',
            'pbl_updated_user_id' => 'Updated User',
            'pbl_expiration_date' => 'Expiration date',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pbl_updated_user_id']);
    }

    /**
     * @return PhoneBlacklistQuery
     */
    public static function find(): PhoneBlacklistQuery
    {
        return new PhoneBlacklistQuery(static::class);
    }
}

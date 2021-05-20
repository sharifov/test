<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\query\PhoneBlacklistLogQuery;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_blacklist_log".
 *
 * @property int $pbll_id
 * @property string $pbll_phone
 * @property string|null $pbll_created_dt
 * @property int|null $pbll_created_user_id
 *
 * @property Employee $pbllCreatedUser
 */
class PhoneBlacklistLog extends \yii\db\ActiveRecord
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pbll_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'attribute' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pbll_created_user_id'],
                ],
                'value' => isset(\Yii::$app->user) ? \Yii::$app->user->id : null,
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['pbll_created_dt', 'safe'],

            ['pbll_created_user_id', 'integer'],
            ['pbll_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pbll_created_user_id' => 'id']],

            ['pbll_phone', 'required'],
            ['pbll_phone', 'string', 'max' => 30],
            ['pbll_phone', PhoneInputValidator::class],
        ];
    }

    public function getPbllCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pbll_created_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'pbll_id' => 'ID',
            'pbll_phone' => 'Phone',
            'pbll_created_dt' => 'Created Dt',
            'pbll_created_user_id' => 'Created User ID',
        ];
    }

    public static function find(): PhoneBlacklistLogQuery
    {
        return new PhoneBlacklistLogQuery(static::class);
    }

    public static function tableName(): string
    {
        return 'phone_blacklist_log';
    }
}

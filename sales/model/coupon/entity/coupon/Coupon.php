<?php

namespace sales\model\coupon\entity\coupon;

use common\models\Employee;
use sales\entities\cases\Cases;
use sales\entities\serializer\Serializable;
use sales\model\coupon\entity\coupon\serializer\CouponSerializer;
use sales\model\coupon\entity\couponCase\CouponCase;
use sales\model\coupon\entity\couponClient\CouponClient;
use sales\model\coupon\entity\couponSend\CouponSend;
use sales\model\coupon\entity\couponUse\CouponUse;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%coupon}}".
 *
 * @property int $c_id
 * @property string $c_code
 * @property float|null $c_amount
 * @property string|null $c_currency_code
 * @property int|null $c_percent
 * @property string|null $c_exp_date
 * @property string|null $c_start_date
 * @property int|null $c_reusable
 * @property int|null $c_reusable_count
 * @property int|null $c_public
 * @property int|null $c_status_id
 * @property int|null $c_disabled
 * @property int|null $c_type_id
 * @property string|null $c_created_dt
 * @property string|null $c_updated_dt
 * @property int|null $c_created_user_id
 * @property int|null $c_updated_user_id
 * @property int $c_used_count
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 * @property Cases[] $cases
 * @property CouponCase[] $couponCases
 * @property CouponUse[] $couponUse
 * @property CouponClient[] $couponClient
 * @property CouponSend[] $couponSend
 */
class Coupon extends ActiveRecord implements Serializable
{
    public function rules(): array
    {
        return [
            ['c_amount', 'required'],
            ['c_amount', 'number'],

            ['c_code', 'required'],
            ['c_code', 'string', 'max' => 50],
            ['c_code', 'unique'],

            ['c_currency_code', 'required'],
            ['c_currency_code', 'string', 'max' => 3],

            ['c_disabled', 'boolean'],

            ['c_exp_date', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['c_percent', 'integer'],

            ['c_public', 'boolean'],

            ['c_reusable', 'boolean'],

            ['c_reusable_count', 'integer'],

            ['c_start_date', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['c_status_id', 'required'],
            ['c_status_id', 'integer'],
            ['c_status_id', 'in', 'range' => array_keys(CouponStatus::getList())],

            ['c_type_id', 'required'],
            ['c_type_id', 'integer'],
            ['c_type_id', 'in', 'range' => array_keys(CouponType::getList())],

            ['c_used_count', 'integer'],
            ['c_used_count', 'default', 'value' => 0],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['c_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['c_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'c_created_user_id',
                'updatedByAttribute' => 'c_updated_user_id',
                'defaultValue' => null, /* TODO:: api handler */ /* */
            ],
        ];
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'c_created_user_id']);
    }

    public function getUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'c_updated_user_id']);
    }

    public function getCases(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Cases::class, ['cs_id' => 'cc_case_id'])->viaTable('{{%coupon_case}}', ['cc_coupon_id' => 'c_id']);
    }

    public function getCouponCases(): \yii\db\ActiveQuery
    {
        return $this->hasMany(CouponCase::class, ['cc_coupon_id' => 'c_id']);
    }

    public function getCouponUse(): \yii\db\ActiveQuery
    {
        return $this->hasMany(CouponUse::class, ['cu_coupon_id' => 'c_id']);
    }

    public function getCouponClient(): \yii\db\ActiveQuery
    {
        return $this->hasMany(CouponClient::class, ['cuc_coupon_id' => 'c_id']);
    }

    public function getCouponSend(): \yii\db\ActiveQuery
    {
        return $this->hasMany(CouponSend::class, ['cus_coupon_id' => 'c_id']);
    }

    public function isSend(): bool
    {
        return $this->c_status_id === CouponStatus::SEND;
    }

    public function isUsed(): bool
    {
        return $this->c_status_id === CouponStatus::USED;
    }

    public function isInProgress(): bool
    {
        return $this->c_status_id === CouponStatus::IN_PROGRESS;
    }

    public function attributeLabels(): array
    {
        return [
            'c_id' => 'ID',
            'c_code' => 'Code',
            'c_amount' => 'Amount',
            'c_currency_code' => 'Currency Code',
            'c_percent' => 'Percent',
            'c_exp_date' => 'Exp Date',
            'c_start_date' => 'Start Date',
            'c_reusable' => 'Reusable',
            'c_reusable_count' => 'Reusable Count',
            'c_public' => 'Public',
            'c_status_id' => 'Status',
            'c_disabled' => 'Disabled',
            'c_type_id' => 'Type',
            'c_created_dt' => 'Created Dt',
            'c_updated_dt' => 'Updated Dt',
            'c_created_user_id' => 'Created User',
            'c_updated_user_id' => 'Updated User',
            'c_used_count' => 'Used count',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%coupon}}';
    }

    public function serialize(): array
    {
        return (new CouponSerializer($this))->getData();
    }

    public function usedCountIncrement(): Coupon
    {
        $this->c_used_count ++;
        return $this;
    }

    public function statusUsed(): Coupon
    {
        $this->c_status_id = CouponStatus::USED;
        return $this;
    }

    public function statusInProgress(): Coupon
    {
        $this->c_status_id = CouponStatus::IN_PROGRESS;
        return $this;
    }
}

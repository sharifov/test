<?php

namespace sales\model\coupon\entity\coupon;

use common\models\Employee;
use sales\entities\cases\Cases;
use sales\model\coupon\entity\couponCase\CouponCase;
use Yii;

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
 * @property string|null $c_used_dt
 * @property int|null $c_disabled
 * @property int|null $c_type_id
 * @property string|null $c_created_dt
 * @property string|null $c_updated_dt
 * @property int|null $c_created_user_id
 * @property int|null $c_updated_user_id
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 * @property Cases[] $cases
 * @property CouponCase[] $couponCases
 */
class Coupon extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['c_amount', 'number'],

            ['c_code', 'required'],
            ['c_code', 'string', 'max' => 50],
            ['c_code', 'unique'],

            ['c_currency_code', 'string', 'max' => 3],

            ['c_disabled', 'integer'],

            ['c_exp_date', 'safe'],

            ['c_percent', 'integer'],

            ['c_public', 'integer'],

            ['c_reusable', 'integer'],

            ['c_reusable_count', 'integer'],

            ['c_start_date', 'datetime'],

            ['c_status_id', 'required'],
            ['c_status_id', 'integer'],
            ['c_status_id', 'in', 'range' => array_keys(CouponStatus::getList())],

            ['c_type_id', 'required'],
            ['c_type_id', 'integer'],
            ['c_type_id', 'in', 'range' => array_keys(CouponType::getList())],

            ['c_used_dt', 'safe'],
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
            'c_used_dt' => 'Used Dt',
            'c_disabled' => 'Disabled',
            'c_type_id' => 'Type ID',
            'c_created_dt' => 'Created Dt',
            'c_updated_dt' => 'Updated Dt',
            'c_created_user_id' => 'Created User',
            'c_updated_user_id' => 'Updated User',
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
}

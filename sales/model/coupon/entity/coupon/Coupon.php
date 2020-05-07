<?php

namespace sales\model\coupon\entity\coupon;

use common\models\Employee;
use sales\entities\cases\Cases;
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
 * @property Employee $cUpdatedUser
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

            ['c_created_dt', 'safe'],

            ['c_created_user_id', 'integer'],
            ['c_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['c_created_user_id' => 'id']],

            ['c_currency_code', 'string', 'max' => 3],

            ['c_disabled', 'integer'],

            ['c_exp_date', 'safe'],

            ['c_percent', 'integer'],

            ['c_public', 'integer'],

            ['c_reusable', 'integer'],

            ['c_reusable_count', 'integer'],

            ['c_start_date', 'safe'],

            ['c_status_id', 'integer'],

            ['c_type_id', 'integer'],

            ['c_updated_dt', 'safe'],

            ['c_updated_user_id', 'integer'],
            ['c_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['c_updated_user_id' => 'id']],

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
        return $this->hasMany(CouponCase::className(), ['cc_coupon_id' => 'c_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'c_id' => 'C ID',
            'c_code' => 'C Code',
            'c_amount' => 'C Amount',
            'c_currency_code' => 'C Currency Code',
            'c_percent' => 'C Percent',
            'c_exp_date' => 'C Exp Date',
            'c_start_date' => 'C Start Date',
            'c_reusable' => 'C Reusable',
            'c_reusable_count' => 'C Reusable Count',
            'c_public' => 'C Public',
            'c_status_id' => 'C Status ID',
            'c_used_dt' => 'C Used Dt',
            'c_disabled' => 'C Disabled',
            'c_type_id' => 'C Type ID',
            'c_created_dt' => 'C Created Dt',
            'c_updated_dt' => 'C Updated Dt',
            'c_created_user_id' => 'C Created User ID',
            'c_updated_user_id' => 'C Updated User ID',
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

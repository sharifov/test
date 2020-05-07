<?php

namespace sales\model\coupon\entity\couponCase;

use common\models\Employee;
use sales\entities\cases\Cases;
use sales\model\coupon\entity\coupon\Coupon;
use Yii;

/**
 * This is the model class for table "{{%coupon_case}}".
 *
 * @property int $cc_coupon_id
 * @property int $cc_case_id
 * @property int|null $cc_sale_id
 * @property string|null $cc_created_dt
 * @property int|null $cc_created_user_id
 *
 * @property Cases $case
 * @property Coupon $coupon
 * @property Employee $createdUser
 */
class CouponCase extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['cc_coupon_id', 'cc_case_id'], 'unique', 'targetAttribute' => ['cc_coupon_id', 'cc_case_id']],

            ['cc_case_id', 'required'],
            ['cc_case_id', 'integer'],
            ['cc_case_id', 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['cc_case_id' => 'cs_id']],

            ['cc_coupon_id', 'required'],
            ['cc_coupon_id', 'integer'],
            ['cc_coupon_id', 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['cc_coupon_id' => 'c_id']],

            ['cc_created_dt', 'safe'],

            ['cc_created_user_id', 'integer'],
            ['cc_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cc_created_user_id' => 'id']],

            ['cc_sale_id', 'integer'],
        ];
    }

    public function getCase(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'cc_case_id']);
    }

    public function getCoupon(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Coupon::class, ['c_id' => 'cc_coupon_id']);
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cc_created_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cc_coupon_id' => 'Cc Coupon ID',
            'cc_case_id' => 'Cc Case ID',
            'cc_sale_id' => 'Cc Sale ID',
            'cc_created_dt' => 'Cc Created Dt',
            'cc_created_user_id' => 'Cc Created User ID',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%coupon_case}}';
    }
}

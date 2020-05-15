<?php

namespace sales\model\coupon\entity\couponCase;

use common\models\Employee;
use sales\entities\cases\Cases;
use sales\model\coupon\entity\coupon\Coupon;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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

            ['cc_sale_id', 'integer'],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cc_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cc_created_user_id'],
                ],
            ],
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
            'cc_coupon_id' => 'Coupon ID',
            'cc_case_id' => 'Case ID',
            'cc_sale_id' => 'Sale ID',
            'cc_created_dt' => 'Created Dt',
            'cc_created_user_id' => 'Created User',
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

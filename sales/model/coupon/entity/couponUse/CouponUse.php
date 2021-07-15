<?php

namespace sales\model\coupon\entity\couponUse;

use sales\model\coupon\entity\coupon\Coupon;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "coupon_use".
 *
 * @property int $cu_id
 * @property int $cu_coupon_id
 * @property string|null $cu_ip
 * @property string|null $cu_user_agent
 * @property string|null $cu_created_dt
 *
 * @property Coupon $cuCoupon
 */
class CouponUse extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cu_coupon_id', 'required'],
            ['cu_coupon_id', 'integer'],
            ['cu_coupon_id', 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['cu_coupon_id' => 'c_id']],

            ['cu_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['cu_ip', 'string', 'max' => 40],
            ['cu_user_agent', 'string', 'max' => 255],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cu_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCuCoupon(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Coupon::class, ['c_id' => 'cu_coupon_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cu_id' => 'ID',
            'cu_coupon_id' => 'Coupon ID',
            'cu_ip' => 'Ip',
            'cu_user_agent' => 'User Agent',
            'cu_created_dt' => 'Created',
        ];
    }

    public static function find(): CouponUseScopes
    {
        return new CouponUseScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'coupon_use';
    }

    public static function create(int $couponId, ?string $ip, ?string $userAgent): CouponUse
    {
        $model = new self();
        $model->cu_coupon_id = $couponId;
        $model->cu_ip = $ip;
        $model->cu_user_agent = $userAgent;
        return $model;
    }
}

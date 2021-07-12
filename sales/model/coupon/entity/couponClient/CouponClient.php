<?php

namespace sales\model\coupon\entity\couponClient;

use Yii;
use sales\model\coupon\entity\coupon\Coupon;
use common\models\Client;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "coupon_client".
 *
 * @property int $cuc_id
 * @property int $cuc_coupon_id
 * @property int $cuc_client_id
 * @property string|null $cuc_created_dt
 *
 * @property Client $cucClient
 * @property Coupon $cucCoupon
 */
class CouponClient extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cuc_client_id', 'required'],
            ['cuc_client_id', 'integer'],
            ['cuc_client_id', 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['cuc_client_id' => 'id']],

            ['cuc_coupon_id', 'required'],
            ['cuc_coupon_id', 'integer'],
            ['cuc_coupon_id', 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['cuc_coupon_id' => 'c_id']],

            ['cuc_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cuc_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCucClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'cuc_client_id']);
    }

    public function getCucCoupon(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Coupon::class, ['c_id' => 'cuc_coupon_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cuc_id' => 'ID',
            'cuc_coupon_id' => 'Coupon',
            'cuc_client_id' => 'Client',
            'cuc_created_dt' => 'Created',
        ];
    }

    public static function find(): CouponClientScopes
    {
        return new CouponClientScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'coupon_client';
    }
}

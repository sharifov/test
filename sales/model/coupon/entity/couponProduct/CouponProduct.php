<?php

namespace sales\model\coupon\entity\couponProduct;

use common\components\validators\CheckJsonValidator;
use modules\product\src\entities\productType\ProductType;
use sales\behaviors\StringToJsonBehavior;
use sales\model\coupon\entity\coupon\Coupon;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "coupon_product".
 *
 * @property int $cup_coupon_id
 * @property int $cup_product_type_id
 * @property string $cup_data_json
 *
 * @property Coupon $cupCoupon
 * @property ProductType $cupProductType
 */
class CouponProduct extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['cup_coupon_id', 'cup_product_type_id'], 'unique', 'targetAttribute' => ['cup_coupon_id', 'cup_product_type_id']],

            ['cup_coupon_id', 'required'],
            ['cup_coupon_id', 'integer'],
            ['cup_coupon_id', 'exist', 'skipOnError' => true, 'targetClass' => Coupon::class, 'targetAttribute' => ['cup_coupon_id' => 'c_id']],

            ['cup_product_type_id', 'required'],
            ['cup_product_type_id', 'integer'],
            ['cup_product_type_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['cup_product_type_id' => 'pt_id']],

            ['cup_data_json', 'required'],
            ['cup_data_json', CheckJsonValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'stringToJson' => [
                'class' => StringToJsonBehavior::class,
                'jsonColumn' => 'cup_data_json',
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCupCoupon(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Coupon::class, ['c_id' => 'cup_coupon_id']);
    }

    public function getCupProductType(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductType::class, ['pt_id' => 'cup_product_type_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cup_coupon_id' => 'Coupon',
            'cup_product_type_id' => 'Product type',
            'cup_data_json' => 'Data Json',
        ];
    }

    public static function find(): CouponProductScopes
    {
        return new CouponProductScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'coupon_product';
    }
}

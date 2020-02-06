<?php

namespace modules\product\src\entities\productType;

use common\models\PaymentMethod;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethod;
use sales\entities\EventTrait;
use sales\helpers\product\ProductQuoteHelper;

/**
 * This is the model class for table "product_type".
 *
 * @property int $pt_id
 * @property string $pt_key
 * @property string $pt_name
 * @property string $pt_description
 * @property double $pt_service_fee_percent
 * @property array $pt_settings
 * @property bool $pt_enabled
 * @property string $pt_created_dt
 * @property string $pt_updated_dt
 *
 * @property ProductTypePaymentMethod[] $productTypePaymentMethod
 */
class ProductType extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const PRODUCT_FLIGHT = 1;
    public const PRODUCT_HOTEL  = 2;

    public const PROCESSING_FEE_AMOUNT = 25.00;

    public static function tableName(): string
    {
        return 'product_type';
    }

    public function rules(): array
    {
        return [
            [['pt_id', 'pt_key', 'pt_name'], 'required'],
            [['pt_id'], 'integer'],
            [['pt_service_fee_percent'], 'number'],
            [['pt_enabled'], 'boolean'],
            [['pt_description'], 'string'],
            [['pt_settings', 'pt_created_dt', 'pt_updated_dt'], 'safe'],
            [['pt_key'], 'string', 'max' => 20],
            [['pt_name'], 'string', 'max' => 50],
            [['pt_id'], 'unique'],
            [['pt_key'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'pt_id' => 'ID',
            'pt_key' => 'Key',
            'pt_name' => 'Name',
            'pt_service_fee_percent' => 'Service Fee percent',
            'pt_description' => 'Description',
            'pt_settings' => 'Settings',
            'pt_enabled' => 'Enabled',
            'pt_created_dt' => 'Created Dt',
            'pt_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getProductTypePaymentMethod(): \yii\db\ActiveQuery
	{
		return $this->hasMany(ProductTypePaymentMethod::class, ['ptpm_produt_type_id' => 'pt_id']);
	}

	/**
	 * @return float
	 */
    public function getProcessingFeeAmount(): float
	{
		$setting = json_decode((string)$this->pt_settings, true);

		return ProductQuoteHelper::roundPrice($setting['processing_fee_amount'] ? (float)$setting['processing_fee_amount'] : self::PROCESSING_FEE_AMOUNT);
	}

	/**
     * @return array
     */
    public static function getList(): array
    {
        return self::find()->select(['pt_name', 'pt_id'])->orderBy(['pt_name' => SORT_ASC])->indexBy('pt_id')->asArray()->column();
    }
}

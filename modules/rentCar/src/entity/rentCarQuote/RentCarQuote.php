<?php

namespace modules\rentCar\src\entity\rentCarQuote;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\interfaces\Quotable;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\serializer\RentCarQuoteSerializer;
use sales\helpers\product\ProductQuoteHelper;
use Yii;

/**
 * This is the model class for table "rent_car_quote".
 *
 * @property int $rcq_id
 * @property int $rcq_rent_car_id
 * @property int $rcq_product_quote_id
 * @property string|null $rcq_hash_key
 * @property string|null $rcq_json_response
 * @property string $rcq_model_name
 * @property string|null $rcq_category
 * @property string|null $rcq_image_url
 * @property string|null $rcq_vendor_name
 * @property string|null $rcq_vendor_logo_url
 * @property string|null $rcq_transmission
 * @property int|null $rcq_seats
 * @property string|null $rcq_doors
 * @property string|null $rcq_options
 * @property int|null $rcq_days
 * @property float $rcq_price_per_day
 * @property string $rcq_currency
 * @property string|null $rcq_advantages
 * @property string|null $rcq_pick_up_location
 * @property string|null $rcq_drop_of_location
 * @property string|null $rcq_created_dt
 * @property string|null $rcq_updated_dt
 * @property int|null $rcq_created_user_id
 * @property int|null $rcq_updated_user_id
 * @property string|null $rcq_request_hash_key
 * @property string|null $rcq_offer_token
 * @property float|null $rcq_system_mark_up
 * @property float|null $rcq_agent_mark_up
 * @property float|null $rcq_service_fee_percent
 *
 * @property ProductQuote $rcqProductQuote
 * @property RentCar $rcqRentCar
 */
class RentCarQuote extends \yii\db\ActiveRecord implements Quotable
{
    public function rules(): array
    {
        return [
            ['rcq_advantages', 'safe'],

            ['rcq_category', 'string', 'max' => 255],

            ['rcq_created_dt', 'safe'],

            ['rcq_created_user_id', 'integer'],

            ['rcq_currency', 'string', 'max' => 3],

            ['rcq_days', 'integer'],

            ['rcq_doors', 'string', 'max' => 50],

            ['rcq_drop_of_location', 'string', 'max' => 255],

            [['rcq_hash_key', 'rcq_request_hash_key'], 'string', 'max' => 32],
            ['rcq_hash_key', 'unique'],

            ['rcq_image_url', 'string', 'max' => 500],

            ['rcq_json_response', 'safe'],

            ['rcq_model_name', 'required'],
            ['rcq_model_name', 'string', 'max' => 255],

            ['rcq_options', 'safe'],

            ['rcq_pick_up_location', 'string', 'max' => 255],

            ['rcq_price_per_day', 'required'],
            ['rcq_price_per_day', 'number'],

            ['rcq_product_quote_id', 'required'],
            ['rcq_product_quote_id', 'integer'],
            ['rcq_product_quote_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['rcq_product_quote_id' => 'pq_id']],

            ['rcq_rent_car_id', 'required'],
            ['rcq_rent_car_id', 'integer'],
            ['rcq_rent_car_id', 'exist', 'skipOnError' => true, 'targetClass' => RentCar::class, 'targetAttribute' => ['rcq_rent_car_id' => 'prc_id']],

            ['rcq_seats', 'integer'],

            ['rcq_transmission', 'string', 'max' => 255],

            ['rcq_updated_dt', 'safe'],

            ['rcq_updated_user_id', 'integer'],

            ['rcq_vendor_logo_url', 'string', 'max' => 500],

            ['rcq_vendor_name', 'string', 'max' => 255],

            ['rcq_offer_token', 'string', 'max' => 500],

            [['rcq_system_mark_up', 'rcq_agent_mark_up', 'rcq_service_fee_percent'], 'default', 'value' => 0.00],
            [['rcq_system_mark_up', 'rcq_agent_mark_up', 'rcq_service_fee_percent'], 'number'],
        ];
    }

    public function getRcqProductQuote(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'rcq_product_quote_id']);
    }

    public function getRcqRentCar(): \yii\db\ActiveQuery
    {
        return $this->hasOne(RentCar::class, ['prc_id' => 'rcq_rent_car_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'rcq_id' => 'ID',
            'rcq_rent_car_id' => 'Rent Car ID',
            'rcq_product_quote_id' => 'Product Quote ID',
            'rcq_hash_key' => 'Hash Key',
            'rcq_offer_token' => 'Offer Token',
            'rcq_json_response' => 'Json Response',
            'rcq_model_name' => 'Model Name',
            'rcq_category' => 'Category',
            'rcq_image_url' => 'Image Url',
            'rcq_vendor_name' => 'Vendor Name',
            'rcq_vendor_logo_url' => 'Vendor Logo Url',
            'rcq_transmission' => 'Transmission',
            'rcq_seats' => 'Seats',
            'rcq_doors' => 'Doors',
            'rcq_options' => 'Options',
            'rcq_days' => 'Days',
            'rcq_price_per_day' => 'Price Per Day',
            'rcq_currency' => 'Currency',
            'rcq_advantages' => 'Advantages',
            'rcq_pick_up_location' => 'Pick Up Location',
            'rcq_drop_of_location' => 'Drop Of Location',
            'rcq_created_dt' => 'Created Dt',
            'rcq_updated_dt' => 'Updated Dt',
            'rcq_created_user_id' => 'Created User ID',
            'rcq_updated_user_id' => 'Updated User ID',
            'rcq_request_hash_key' => 'Request hash key',
            'rcq_system_mark_up' => 'System mark up',
            'rcq_agent_mark_up' => 'Agent mark up',
        ];
    }

    public static function find(): RentCarQuoteScopes
    {
        return new RentCarQuoteScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'rent_car_quote';
    }

    public function isBookable(): bool
    {
        return !$this->rcqProductQuote->isDeclined();
    }

    public static function findByProductQuote(int $productQuoteId): ?Quotable
    {
        return self::findOne(['rcq_product_quote_id' => $productQuoteId]);
    }

    public function serialize(): array
    {
        return (new RentCarQuoteSerializer($this))->getData();
    }

    public function getId(): int
    {
        return $this->rcq_id;
    }

    public function getProcessingFee(): float
    {
        $processingFeeAmount = $this->rcqProductQuote->pqProduct->prType->getProcessingFeeAmount();
        return ProductQuoteHelper::roundPrice($processingFeeAmount);
    }

    public function getSystemMarkUp(): float
    {
        return $this->rcq_system_mark_up;
    }

    public function getAgentMarkUp(): float
    {
        return $this->rcq_agent_mark_up;
    }
}

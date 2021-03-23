<?php

namespace modules\rentCar\src\entity\rentCarQuote;

use common\models\Client;
use common\models\Lead;
use common\models\Project;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\interfaces\ProductDataInterface;
use modules\product\src\interfaces\Quotable;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\serializer\RentCarQuoteSerializer;
use sales\entities\EventTrait;
use sales\helpers\product\ProductQuoteHelper;
use Yii;
use yii\helpers\ArrayHelper;

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
 * @property string $rcq_car_reference_id
 * @property array|null $rcq_booking_json
 * @property string|null $rcq_booking_id
 * @property array|null $rcq_contract_request_json
 * @property string|null $rcq_pick_up_dt
 * @property string|null $rcq_drop_off_dt
 *
 * @property ProductQuote $rcqProductQuote
 * @property RentCar $rcqRentCar
 */
class RentCarQuote extends \yii\db\ActiveRecord implements Quotable, ProductDataInterface
{
    use EventTrait;

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

            ['rcq_car_reference_id', 'string'],

            ['rcq_booking_json', 'safe'],

            ['rcq_booking_id', 'string', 'max' => 255],

            ['rcq_contract_request_json', 'safe'],

            [['rcq_pick_up_dt', 'rcq_drop_off_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s', 'skipOnError' => true, 'skipOnEmpty' => true],
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
            'rcq_drop_of_location' => 'Drop Off Location',
            'rcq_created_dt' => 'Created Dt',
            'rcq_updated_dt' => 'Updated Dt',
            'rcq_created_user_id' => 'Created User ID',
            'rcq_updated_user_id' => 'Updated User ID',
            'rcq_request_hash_key' => 'Request hash key',
            'rcq_system_mark_up' => 'System mark up',
            'rcq_agent_mark_up' => 'Agent mark up',
            'rcq_car_reference_id' => 'Car reference ID',
            'rcq_booking_json' => 'Booking json',
            'rcq_booking_id' => 'Booking ID',
            'rcq_contract_request_json' => 'Contract request json',
            'rcq_pick_up_dt' => 'Pick Up DT',
            'rcq_drop_off_dt' => 'Drop Off DT',
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
        return (ProductQuoteStatus::isBookable($this->rcqProductQuote->pq_status_id));
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

    public function getProject(): Project
    {
        return $this->rcqProductQuote->pqProduct->prLead->project;
    }

    public function getLead(): Lead
    {
        return $this->rcqProductQuote->pqProduct->prLead;
    }

    public function getClient(): Client
    {
        return $this->rcqProductQuote->pqProduct->prLead->client;
    }

    public function getOrder(): ?Order
    {
        if ($order = ArrayHelper::getValue($this, 'rcqProductQuote.pqOrder')) {
            /** @var Order $order */
            return $order;
        }
        return null;
    }
}

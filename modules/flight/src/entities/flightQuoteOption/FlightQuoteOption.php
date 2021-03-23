<?php

namespace modules\flight\src\entities\flightQuoteOption;

use common\models\Currency;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteTrip;
use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "flight_quote_option".
 *
 * @property int $fqo_id
 * @property int $fqo_product_quote_option_id
 * @property int|null $fqo_flight_pax_id
 * @property int|null $fqo_flight_quote_segment_id
 * @property int|null $fqo_flight_quote_trip_id
 * @property string|null $fqo_display_name
 * @property float|null $fqo_markup_amount
 * @property float|null $fqo_usd_markup_amount
 * @property float|null $fqo_base_price
 * @property float|null $fqo_usd_base_price
 * @property float|null $fqo_total_price
 * @property float|null $fqo_usd_total_price
 * @property string|null $fqo_created_dt
 * @property string|null $fqo_updated_dt
 * @property string|null $fqo_currency
 *
 * @property Currency $fqoCurrency
 * @property FlightPax $fqoFlightPax
 * @property FlightQuoteSegment $fqoFlightQuoteSegment
 * @property FlightQuoteTrip $fqoFlightQuoteTrip
 */
class FlightQuoteOption extends \yii\db\ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fqo_created_dt', 'fqo_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['fqo_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['fqo_base_price', 'number'],

            ['fqo_created_dt', 'safe'],
            ['fqo_currency', 'string', 'max' => 5],
            ['fqo_currency', 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['fqo_currency' => 'cur_code']],

            ['fqo_display_name', 'string', 'max' => 255],

            ['fqo_flight_pax_id', 'integer'],
            ['fqo_flight_pax_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightPax::class, 'targetAttribute' => ['fqo_flight_pax_id' => 'fp_id']],

            ['fqo_flight_quote_segment_id', 'integer'],
            ['fqo_flight_quote_segment_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteSegment::class, 'targetAttribute' => ['fqo_flight_quote_segment_id' => 'fqs_id']],

            ['fqo_flight_quote_trip_id', 'integer'],
            ['fqo_flight_quote_trip_id', 'exist', 'skipOnError' => true, 'targetClass' => FlightQuoteTrip::class, 'targetAttribute' => ['fqo_flight_quote_trip_id' => 'fqt_id']],

            ['fqo_markup_amount', 'number'],

            ['fqo_product_quote_option_id', 'required'],
            ['fqo_product_quote_option_id', 'integer'],
            ['fqo_product_quote_option_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuoteOption::class, 'targetAttribute' => ['fqo_product_quote_option_id' => 'pqo_id']],

            ['fqo_total_price', 'number'],

            ['fqo_usd_base_price', 'number'],

            ['fqo_usd_markup_amount', 'number'],

            ['fqo_usd_total_price', 'number'],

            ['fqo_updated_dt', 'safe'],
        ];
    }

    public function getFqoCurrency(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'fqo_currency']);
    }

    public function getFqoFlightPax(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FlightPax::class, ['fp_id' => 'fqo_flight_pax_id']);
    }

    public function getFqoFlightQuoteSegment(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FlightQuoteSegment::class, ['fqs_id' => 'fqo_flight_quote_segment_id']);
    }

    public function getFqoFlightQuoteTrip(): \yii\db\ActiveQuery
    {
        return $this->hasOne(FlightQuoteTrip::class, ['fqt_id' => 'fqo_flight_quote_trip_id']);
    }

    public function getFqoProductQuoteOption(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuoteOption::class, ['pqo_id' => 'fqo_product_quote_option_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fqo_id' => 'ID',
            'fqo_product_quote_option_id' => 'Product Quote Option ID',
            'fqo_flight_pax_id' => 'Flight Pax ID',
            'fqo_flight_quote_segment_id' => 'Flight Quote Segment ID',
            'fqo_flight_quote_trip_id' => 'Flight Quote Trip ID',
            'fqo_display_name' => 'Display Name',
            'fqo_markup_amount' => 'Markup Amount',
            'fqo_base_price' => 'Base Price',
            'fqo_total_price' => 'Total Price',
            'fqo_client_total' => 'Client Total',
            'fqo_created_dt' => 'Created Dt',
            'fqo_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'flight_quote_option';
    }

    public static function create(
        int $productQuoteOptionId,
        ?int $flightPaxId,
        ?int $flightQuoteSegmentId,
        ?int $flightQuoteTripId,
        ?string $displayName,
        ?float $markupAmount,
        ?float $usdMarkupAmount,
        ?float $basePrice,
        ?float $usdBasePrice,
        ?float $totalPrice,
        ?float $usdTotalPrice,
        ?string $currency
    ): self {
        $option = new self();
        $option->fqo_product_quote_option_id = $productQuoteOptionId;
        $option->fqo_flight_pax_id = $flightPaxId;
        $option->fqo_flight_quote_segment_id = $flightQuoteSegmentId;
        $option->fqo_flight_quote_trip_id = $flightQuoteTripId;
        $option->fqo_display_name = $displayName;
        $option->fqo_markup_amount = $markupAmount;
        $option->fqo_usd_markup_amount = $usdMarkupAmount;
        $option->fqo_base_price = $basePrice;
        $option->fqo_usd_base_price = $usdBasePrice;
        $option->fqo_total_price = $totalPrice;
        $option->fqo_usd_total_price = $usdTotalPrice;
        $option->fqo_currency = $currency;
        return $option;
    }
}

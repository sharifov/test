<?php

namespace modules\cruise\src\entity\cruiseQuote;

use modules\cruise\src\entity\cruise\Cruise;
use modules\cruise\src\entity\cruiseQuote\serializer\CruiseQuoteSerializer;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\interfaces\Quotable;
use sales\helpers\product\ProductQuoteHelper;
use Yii;

/**
 * This is the model class for table "{{%cruise_quote}}".
 *
 * @property int $crq_id
 * @property string|null $crq_hash_key
 * @property int|null $crq_product_quote_id
 * @property int|null $crq_cruise_id
 * @property string|null $crq_data_json
 *
 * @property Cruise $cruise
 * @property ProductQuote $productQuote
 */
class CruiseQuote extends \yii\db\ActiveRecord implements Quotable
{
    public function rules(): array
    {
        return [
            ['crq_cruise_id', 'integer'],
            ['crq_cruise_id', 'exist', 'skipOnError' => true, 'targetClass' => Cruise::class, 'targetAttribute' => ['crq_cruise_id' => 'crs_id']],

            ['crq_data_json', 'safe'],

            ['crq_hash_key', 'string', 'max' => 50],

            ['crq_product_quote_id', 'integer'],
            ['crq_product_quote_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['crq_product_quote_id' => 'pq_id']],
        ];
    }

    public function getCruise(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Cruise::class, ['crs_id' => 'crq_cruise_id']);
    }

    public function getProductQuote(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'crq_product_quote_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'crq_id' => 'ID',
            'crq_hash_key' => 'Hash Key',
            'crq_product_quote_id' => 'Product Quote ID',
            'crq_cruise_id' => 'Cruise ID',
            'crq_data_json' => 'Data Json',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%cruise_quote}}';
    }

    public static function findByProductQuote(int $productQuoteId): ?Quotable
    {
        return self::find()->byProductQuote($productQuoteId)->limit(1)->one();
    }

    public function serialize(): array
    {
        return (new CruiseQuoteSerializer($this))->getData();
    }

    public function getId(): int
    {
        return $this->crq_id;
    }

    public function getProcessingFee(): float
    {
        $processingFeeAmount = $this->productQuote->pqProduct->prType->getProcessingFeeAmount();
        $result = ($this->getAdults() + $this->getChildren()) * $processingFeeAmount;
        return ProductQuoteHelper::roundPrice($result);
    }

    public function getAdults(): int
    {
        return $this->cruise->getAdults();
    }

    public function getChildren(): int
    {
        return $this->cruise->getChildren();
    }

    public function getSystemMarkUp(): float
    {
        $result = 0.00;
//        foreach ($this->hotelQuoteRooms as $room) {
//            $result += $room->hqr_system_mark_up;
//        }
        return $result;
    }

    public function getAgentMarkUp(): float
    {
        $result = 0.00;
//        foreach ($this->hotelQuoteRooms as $room) {
//            $result += $room->hqr_agent_mark_up;
//        }
        return $result;
    }
}

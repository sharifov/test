<?php

namespace modules\cruise\src\entity\cruise;

use modules\cruise\src\entity\cruise\serializer\CruiseSerializer;
use modules\cruise\src\entity\cruiseCabin\CruiseCabin;
use modules\cruise\src\useCase\updateCruiseRequest\CruiseUpdateRequestForm;
use modules\product\src\entities\product\Product;
use modules\product\src\interfaces\Productable;
use sales\entities\EventTrait;
use Yii;

/**
 * This is the model class for table "{{%cruise}}".
 *
 * @property int $crs_id
 * @property int|null $crs_product_id
 * @property string|null $crs_departure_date_from
 * @property string|null $crs_arrival_date_to
 * @property string|null $crs_destination_code
 * @property string|null $crs_destination_label
 *
 * @property Product $product
 * @property CruiseCabin[] $cabins
 */
class Cruise extends \yii\db\ActiveRecord implements Productable
{
    use EventTrait;

    public static function create(int $productId): self
    {
        $cruise = new static();
        $cruise->crs_product_id = $productId;
        return $cruise;
    }

    public function updateRequest(CruiseUpdateRequestForm $form): void
    {
        $this->attributes = $form->attributes;
    }

    public function rules(): array
    {
        return [
            ['crs_arrival_date_to', 'safe'],

            ['crs_departure_date_from', 'safe'],

            ['crs_destination_code', 'string', 'max' => 10],

            ['crs_destination_label', 'string', 'max' => 50],

            ['crs_product_id', 'integer'],
            ['crs_product_id', 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['crs_product_id' => 'pr_id']],
        ];
    }

    public function getProduct(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'crs_product_id']);
    }

    public function getCabins(): \yii\db\ActiveQuery
    {
        return $this->hasMany(CruiseCabin::class, ['crc_cruise_id' => 'crs_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'crs_id' => 'ID',
            'crs_product_id' => 'Product ID',
            'crs_departure_date_from' => 'Departure Date From',
            'crs_arrival_date_to' => 'Arrival Date To',
            'crs_destination_code' => 'Destination Code',
            'crs_destination_label' => 'Destination Label',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%cruise}}';
    }

    public function serialize(): array
    {
        return (new CruiseSerializer($this))->getData();
    }

    public function getId(): int
    {
        return $this->crs_id;
    }

    public static function findByProduct(int $productId): ?Productable
    {
        return self::find()->byProduct($productId)->limit(1)->one();
    }
}

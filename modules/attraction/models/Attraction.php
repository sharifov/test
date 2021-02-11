<?php

namespace modules\attraction\models;

use modules\product\src\interfaces\Productable;
use modules\product\src\entities\product\Product;
use sales\entities\EventTrait;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "attraction".
 *
 * @property int $atn_id
 * @property int|null $atn_product_id
 *  @property string|null $atn_date_from
 * @property string|null $atn_date_to
 * @property string|null $atn_destination
 *
 * @property Product $atnProduct
 */
class Attraction extends \yii\db\ActiveRecord implements Productable
{
    use EventTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attraction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atn_product_id'], 'integer'],
            [['atn_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['atn_product_id' => 'pr_id']],

            [['atn_date_from', 'atn_date_to'], 'safe'],
            [['atn_destination'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'atn_id' => 'ID',
            'atn_product_id' => 'Product ID',
            'atn_date_from' => 'Date From',
            'atn_date_to' => 'Date To',
            'atn_destination' => 'Destination',
        ];
    }

    public static function create(int $productId): self
    {
        $attraction = new static();
        $attraction->atn_product_id = $productId;
        return $attraction;
    }

    public function getId(): int
    {
        return $this->atn_id;
    }

    public function serialize(): array
    {
        return (new AttractionSerializer($this))->getData();
    }

    public static function findByProduct(int $productId): ?Productable
    {
        return self::find()->byProduct($productId)->limit(1)->one();
    }

    /**
     * Gets query for [[AtnProduct]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtnProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'atn_product_id']);
    }
}

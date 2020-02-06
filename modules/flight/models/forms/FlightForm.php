<?php

namespace modules\flight\models\forms;

use modules\product\src\entities\product\Product;
use Yii;
use yii\base\Model;

/**
 * This is the model Form class for table "flight".
 *
 * @property int $fl_id
 * @property int|null $fl_product_id
 * @property int|null $fl_trip_type_id
 * @property string|null $fl_cabin_class
 * @property int|null $fl_adults
 * @property int|null $fl_children
 * @property int|null $fl_infants
 *
 */
class FlightForm extends Model
{
    public $fl_id;
    public $fl_product_id;
    public $fl_trip_type_id;
    public $fl_cabin_class;
    public $fl_adults;
    public $fl_children;
    public $fl_infants;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['fl_product_id', 'fl_trip_type_id', 'fl_adults', 'fl_children', 'fl_infants'], 'integer'],
            [['fl_cabin_class'], 'string', 'max' => 1],
            [['fl_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['fl_product_id' => 'pr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fl_id' => 'ID',
            'fl_product_id' => 'Product ID',
            'fl_trip_type_id' => 'Trip Type ID',
            'fl_cabin_class' => 'Cabin Class',
            'fl_adults' => 'Adults',
            'fl_children' => 'Children',
            'fl_infants' => 'Infants',
        ];
    }

}

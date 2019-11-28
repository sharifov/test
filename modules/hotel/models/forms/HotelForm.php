<?php

namespace modules\hotel\models\forms;

use common\models\Product;
use Yii;
use yii\base\Model;

/**
 * This is the model class for table "hotel".
 *
 * @property int $ph_id
 * @property int|null $ph_product_id
 * @property string|null $ph_check_in_date
 * @property string|null $ph_check_out_date
 * @property string|null $ph_destination_code
 * @property int|null $ph_min_star_rate
 * @property int|null $ph_max_star_rate
 * @property int|null $ph_max_price_rate
 * @property int|null $ph_min_price_rate
 *
 */
class HotelForm extends Model
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ph_product_id', 'ph_min_star_rate', 'ph_max_star_rate', 'ph_max_price_rate', 'ph_min_price_rate'], 'integer'],
            [['ph_check_in_date', 'ph_check_out_date'], 'safe'],
            [['ph_destination_code'], 'string', 'max' => 10],
            [['ph_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['ph_product_id' => 'pr_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ph_id' => 'ID',
            'ph_product_id' => 'Product ID',
            'ph_check_in_date' => 'Check In Date',
            'ph_check_out_date' => 'Check Out Date',
            'ph_destination_code' => 'Destination Code',
            'ph_min_star_rate' => 'Min Star Rate',
            'ph_max_star_rate' => 'Max Star Rate',
            'ph_max_price_rate' => 'Max Price Rate',
            'ph_min_price_rate' => 'Min Price Rate',
        ];
    }

}

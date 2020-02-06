<?php

namespace modules\hotel\models\forms;

use modules\product\src\entities\product\Product;
use Yii;
use yii\base\Model;

/**
 * This is the form model class for table "hotel".
 *
 * @property int $ph_id
 * @property int|null $ph_product_id
 * @property string|null $ph_check_in_date
 * @property string|null $ph_check_out_date
 * @property integer|null $ph_zone_code
 * @property integer|null $ph_hotel_code
 * @property string|null $ph_destination_code
 * @property string|null $ph_destination_label
 * @property int|null $ph_min_star_rate
 * @property int|null $ph_max_star_rate
 * @property int|null $ph_max_price_rate
 * @property int|null $ph_min_price_rate
 *
 */
class HotelForm extends Model
{
    public $ph_id;
    public $ph_product_id;
    public $ph_check_in_date;
    public $ph_check_out_date;
    public $ph_zone_code;
    public $ph_hotel_code;
    public $ph_destination_code;
    public $ph_destination_label;
    public $ph_min_star_rate;
    public $ph_max_star_rate;
    public $ph_max_price_rate;
    public $ph_min_price_rate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ph_check_in_date', 'ph_check_out_date', 'ph_destination_code'], 'required'],
            [['ph_product_id', 'ph_min_star_rate', 'ph_max_star_rate', 'ph_max_price_rate', 'ph_min_price_rate', 'ph_zone_code', 'ph_hotel_code'], 'integer'],
            [['ph_check_in_date', 'ph_check_out_date'], 'safe'],
			[['ph_zone_code', 'ph_hotel_code'], 'string', 'max' => 11],
			[['ph_destination_code'], 'string', 'max' => 10],
            [['ph_destination_label'], 'string', 'max' => 100],
            [['ph_product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['ph_product_id' => 'pr_id']],
			[['ph_check_in_date', 'ph_check_out_date'], 'filter', 'filter' => static function ($value) {
        		return date('Y-m-d', strtotime($value));
			}],
			[['ph_check_in_date', 'ph_check_out_date'], 'date', 'format' => 'php:Y-m-d'],
			['ph_check_in_date', 'compare', 'compareAttribute' => 'ph_check_out_date', 'operator' => '<', 'enableClientValidation' => true]
		];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
		return [
			'ph_product_id' => 'Product ID',
			'ph_check_in_date' => 'Check In',
			'ph_check_out_date' => 'Check Out',
			'ph_zone_code' => 'Zone Code',
			'ph_hotel_code' => 'Hotel Code',
			'ph_destination_code' => 'Destination',
			'ph_destination_label' => 'Destination',
			'ph_min_star_rate' => 'Min. Rate',
			'ph_max_star_rate' => 'Max. Rate',
			'ph_max_price_rate' => 'Max Price Rate',
			'ph_min_price_rate' => 'Min Price Rate',
		];
    }

}

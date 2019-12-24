<?php

namespace modules\hotel\models\forms;

use common\models\Product;
use Yii;
use yii\base\Model;

/**
 * This is the form model class for table "hotel".
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
    public $ph_id;
    public $ph_product_id;
    public $ph_check_in_date;
    public $ph_check_out_date;
    public $ph_destination_code;
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
            'ph_product_id' => 'Product ID',
            'ph_check_in_date' => 'Check In',
            'ph_check_out_date' => 'Check Out',
            'ph_destination_code' => 'Destination',
            'ph_min_star_rate' => 'Min. Rate',
            'ph_max_star_rate' => 'Max. Rate',
            'ph_max_price_rate' => 'Max Price Rate',
            'ph_min_price_rate' => 'Min Price Rate',
        ];
    }

}

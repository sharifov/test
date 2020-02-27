<?php

namespace modules\hotel\src\useCases\request\update;

use modules\hotel\models\Hotel;
use yii\base\Model;

/**
 * Class HotelUpdateRequestForm
 *
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
 * @property int $hotelId
 */
class HotelUpdateRequestForm extends Model
{
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

    private $hotelId;

    public function __construct(Hotel $hotel, $config = [])
    {
        parent::__construct($config);
        $this->hotelId = $hotel->ph_id;
        $this->load($hotel->getAttributes(), '');
        $this->load($hotel->phProduct->getAttributes(), '');
    }

    public function rules(): array
    {
        return [
            ['ph_check_in_date', 'required'],
            ['ph_check_in_date', 'filter', 'filter' => static function ($value) {
                return date('Y-m-d', strtotime($value));
            }],
            ['ph_check_in_date', 'date', 'format' => 'php:Y-m-d'],

            ['ph_check_out_date', 'required'],
            ['ph_check_out_date', 'filter', 'filter' => static function ($value) {
                return date('Y-m-d', strtotime($value));
            }],
            ['ph_check_out_date', 'date', 'format' => 'php:Y-m-d'],

            ['ph_check_in_date', 'compare', 'compareAttribute' => 'ph_check_out_date', 'operator' => '<', 'enableClientValidation' => true],

            ['ph_destination_code', 'required'],
            ['ph_destination_code', 'string', 'max' => 10],

            ['ph_destination_label', 'string', 'max' => 100],

            ['ph_min_star_rate', 'integer'],

            ['ph_max_star_rate', 'integer'],

            ['ph_max_price_rate', 'integer'],

            ['ph_min_price_rate', 'integer'],

            ['ph_zone_code', 'integer'],
            ['ph_zone_code', 'string', 'max' => 11],

            ['ph_hotel_code', 'integer'],
            ['ph_hotel_code', 'string', 'max' => 11],
		];
    }

    public function attributeLabels(): array
    {
		return [
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

    public function getHotelId(): int
    {
        return $this->hotelId;
    }
}

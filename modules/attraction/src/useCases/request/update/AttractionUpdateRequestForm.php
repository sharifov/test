<?php

namespace modules\attraction\src\useCases\request\update;

use modules\attraction\models\Attraction;
use yii\base\Model;

/**
 * Class HotelUpdateRequestForm
 *
 * @property int|null $atn_product_id
 * @property string|null $atn_date_from
 * @property string|null $atn_date_to
 * @property string|null $atn_destination_code
 *
 * @property int $attractionId
 */
class AttractionUpdateRequestForm extends Model
{
    public $atn_date_from;
    public $atn_date_to;
    public $atn_destination;
    public $atn_destination_code;

    private $attractionId;

    public function __construct(Attraction $attraction, $config = [])
    {
        parent::__construct($config);
        $this->attractionId = $attraction->atn_id;
        $this->load($attraction->getAttributes(), '');
        $this->load($attraction->atnProduct->getAttributes(), '');
    }

    public function rules(): array
    {
        return [
            ['atn_date_from', 'required'],
            ['atn_date_from', 'filter', 'filter' => static function ($value) {
                return date('Y-m-d', strtotime($value));
            }],
            ['atn_date_from', 'date', 'format' => 'php:Y-m-d'],

            ['atn_date_to', 'required'],
            ['atn_date_to', 'filter', 'filter' => static function ($value) {
                return date('Y-m-d', strtotime($value));
            }],
            ['atn_date_to', 'date', 'format' => 'php:Y-m-d'],

            ['atn_date_from', 'compare', 'compareAttribute' => 'atn_date_to', 'operator' => '<', 'enableClientValidation' => true],

            ['atn_destination', 'string', 'max' => 100],
            ['atn_destination_code', 'string', 'max' => 10],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'atn_date_from' => 'Date From',
            'atn_date_to' => 'Check Out',
            'atn_destination' => 'Destination',
            'atn_destination_code' => 'Destination code',
        ];
    }

    public function getAttractionId(): int
    {
        return $this->attractionId;
    }
}

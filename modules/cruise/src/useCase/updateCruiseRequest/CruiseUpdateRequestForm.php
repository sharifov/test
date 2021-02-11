<?php

namespace modules\cruise\src\useCase\updateCruiseRequest;

use modules\cruise\src\entity\cruise\Cruise;
use yii\base\Model;

/**
 * Class CruiseUpdateRequestForm
 *
 * @property string|null $crs_departure_date_from
 * @property string|null $crs_arrival_date_to
 * @property string|null $crs_destination_code
 * @property string|null $crs_destination_label
 *
 * @property int $cruiseId
 */
class CruiseUpdateRequestForm extends Model
{
    public $crs_departure_date_from;
    public $crs_arrival_date_to;
    public $crs_destination_code;
    public $crs_destination_label;

    private $cruiseId;

    public function __construct(Cruise $cruise, $config = [])
    {
        parent::__construct($config);
        $this->cruiseId = $cruise->crs_id;
        $this->load($cruise->getAttributes(), '');
    }

    public function rules(): array
    {
        return [
            ['crs_departure_date_from', 'required'],
            [
                'crs_departure_date_from',
                'filter',
                'filter' => static function ($value) {
                    return date('Y-m-d', strtotime($value));
                }
            ],
            ['crs_departure_date_from', 'date', 'format' => 'php:Y-m-d'],

            ['crs_arrival_date_to', 'required'],
            [
                'crs_arrival_date_to',
                'filter',
                'filter' => static function ($value) {
                    return date('Y-m-d', strtotime($value));
                }
            ],
            ['crs_arrival_date_to', 'date', 'format' => 'php:Y-m-d'],

            [
                'crs_departure_date_from',
                'compare',
                'compareAttribute' => 'crs_arrival_date_to',
                'operator' => '<',
                'enableClientValidation' => true
            ],

            ['crs_destination_code', 'required'],
            ['crs_destination_code', 'string', 'max' => 50],
            ['crs_destination_code', 'in', 'range' => array_keys($this->getDestinations())],
        ];
    }

    public function afterValidate()
    {
        parent::afterValidate();
        if (!$this->hasErrors()) {
            $this->crs_destination_label = $this->getDestinations()[$this->crs_destination_code] ?? null;
        }
    }

    public function attributeLabels(): array
    {
        return [
            'crs_departure_date_from' => 'Departure Date From',
            'crs_arrival_date_to' => 'Arrival Date To',
            'crs_destination_code' => 'Destination',
        ];
    }

    public function getCruiseId(): int
    {
        return $this->cruiseId;
    }

    public function getDestinations(): array
    {
        return [
            "caribbean" => "Caribbean",
            "bahamas" => "Bahamas",
            "mexico" => "Mexico",
            "alaska" => "Alaska",
            "europe" => "Europe",
            "bermuda" => "Bermuda",
            "hawaii" => "Hawaii",
            "canada-new-england" => "Canada / New England",
            "arctic-antarctic" => "Arctic / Antarctic",
            "middle-east" => "Middle East",
            "africa" => "Africa",
            "panama-canal" => "Panama Canal",
            "asia" => "Asia",
            "pacific-coastal" => "Pacific Coastal",
            "australia-new-zealand" => "Australia / New Zealand",
            "central-america" => "Central America",
            "galapagos" => "Galapagos",
            "getaway-at-sea" => "Getaway at Sea",
            "transatlantic" => "Transatlantic",
            "world-cruise" => "World",
            "south-america" => "South America",
            "south-pacific" => "South Pacific",
            "transpacific" => "Transpacific"
        ];
    }
}

<?php

namespace modules\cruise\src\services\search;

use modules\cruise\src\entity\cruise\Cruise;
use yii\base\Model;

class Params extends Model
{
    public $date_from;
    public $date_to;
    public $destination;
    public $adult;
    public $child;

    public function __construct(Cruise $cruise, $config = [])
    {
        parent::__construct($config);
        $this->date_from = $cruise->crs_departure_date_from;
        $this->date_to = $cruise->crs_arrival_date_to;
        $this->destination = $cruise->crs_destination_code;
        $this->adult = $cruise->getAdults();
        $this->child = $cruise->getChildren();
    }

    public function rules(): array
    {
        return [
            [['date_from', 'date_to'], 'required'],
            [['date_from', 'date_to'], 'string', 'max' => 15],
            [['destination'], 'string', 'max' => 100],
            [['adult'], 'integer', 'max' => 9],
            [['child'], 'integer', 'max' => 9],
        ];
    }
}

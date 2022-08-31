<?php

namespace modules\quoteAward\src\forms;

use common\components\validators\IataValidator;
use modules\quoteAward\src\models\SegmentAwardQuoteItem;
use yii\base\Model;

class SegmentAwardQuoteForm extends Model
{
    public $origin;
    public $destination;
    public $departure;
    public $arrival;
    public $trip;
    public $flight;
    public $flight_number;

    public function __construct(SegmentAwardQuoteItem $segment, $config = [])
    {
        $this->attributes = $segment->attributes;

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['origin', 'destination'], 'required'],
            [['origin', 'destination'], IataValidator::class],
            ['destination', 'compare', 'compareAttribute' => 'origin', 'operator' => '!='],
            [['origin', 'destination'], 'filter', 'filter' => function ($value) {
                return strtoupper($value);
            }],

            ['departure', 'required'],
            ['departure', 'date', 'format' => 'php:d-M-Y H:i'],
            ['departure', 'filter', 'filter' => function ($value) {
                return date('d-m-Y H:i', strtotime($value));
            }],

            ['arrival', 'required'],
            ['arrival', 'date', 'format' => 'php:d-M-Y'],
            ['arrival', 'filter', 'filter' => function ($value) {
                return date('d-m-Y H:i', strtotime($value));
            }],

            ['trip', 'required'],
            ['arrival', 'in', 'range' => SegmentAwardQuoteItem::getTrips()],

            ['flight', 'required'],
            ['flight_number', 'required'],
        ];
    }
}

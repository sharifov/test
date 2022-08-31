<?php

namespace modules\quoteAward\src\forms;

use modules\quoteAward\src\models\FlightAwardQuoteItem;
use yii\base\Model;

class PriceListAwardQuoteForm extends Model
{
    public $flight;
    public $passenger_type;
    public $passenger_count;
    public $selling;
    public $net;
    public $taxes;
    public $fare;
    public $mark_up;

    public $is_required_award_program;
    public $award_program;
    public $miles;
    public $ppm;

    public function rules(): array
    {
        return [
            [['flight', 'passenger_type', 'selling',
                'net', 'taxes', 'mark_up', 'award_program', 'miles', 'ppm', 'passenger_count'], 'safe'],

            [['flight', 'passenger_type', 'selling',
                'net', 'taxes', 'mark_up', 'passenger_count'], 'required'],
        ];
    }

    public function __construct(int $flight_id, string $passenger_type, int $passenger_count, bool $is_required_award_program = false, $config = [])
    {
        $this->flight = $flight_id;
        $this->passenger_type = $passenger_type;
        $this->passenger_count = $passenger_count;
        $this->is_required_award_program = $is_required_award_program;
        parent::__construct($config);
    }
}

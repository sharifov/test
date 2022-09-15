<?php

namespace modules\quoteAward\src\models;

use common\models\Lead;
use yii\base\Model;

class PriceListAwardQuoteItem extends Model
{
    public $flight;
    public $passenger_type;
    public $selling;
    public $net;
    public $taxes;
    public $mark_up;

    public $award_program;
    public $miles;
    public $ppm;

    public function rules(): array
    {
        return [
            [['flight', 'passenger_type', 'selling',
                'net', 'taxes', 'mark_up', 'award_program', 'miles', 'ppm'], 'safe'],
        ];
    }

    public function __construct(?Lead $lead = null, $config = [])
    {
        if ($lead) {
            $this->cabin = $lead->cabin;
            $this->adults = $lead->adults;
            $this->children = $lead->children;
            $this->infants = $lead->infants;
        }

        parent::__construct($config);
    }
}

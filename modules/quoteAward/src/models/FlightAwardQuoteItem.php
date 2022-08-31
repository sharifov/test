<?php

namespace modules\quoteAward\src\models;

use common\models\Lead;
use modules\flight\models\Flight;
use yii\base\Model;

class FlightAwardQuoteItem extends Model
{
    public $id;
    public $cabin;
    public $adults;
    public $children;
    public $infants;
    public $gds;
    public $validationCarrier;
    public $recordLocator;
    public $fareType;
    public $quoteProgram;

    public function rules(): array
    {
        return [
            [['cabin', 'validationCarrier', 'cabin',
                'gds', 'adults', 'children', 'infants', 'recordLocator', 'fareType', 'quoteProgram', 'id'], 'safe'],
        ];
    }


    public function __construct(int $id, ?Lead $lead = null, $config = [])
    {
        if ($lead) {
            $this->cabin = $lead->cabin;
            $this->adults = $lead->adults;
            $this->children = $lead->children;
            $this->infants = $lead->infants;
        }
        $this->id = $id;

        parent::__construct($config);
    }
}

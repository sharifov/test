<?php

namespace sales\model\lead\useCases\lead\api\create;

use common\models\Airports;
use yii\base\Model;

/**
 * Class FlightForm
 *
 * @property string $origin
 * @property string $originLabel
 * @property string $destination
 * @property string $destinationLabel
 * @property string $departure
 */
class FlightForm extends Model
{
    public $origin;
    public $originLabel;
    public $destination;
    public $destinationLabel;
    public $departure;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['origin', 'destination'], 'filter', 'filter' => static function ($value) {
                return strtoupper($value);
            }],

            ['origin', 'required'],
            ['origin', 'iataValidation'],

            ['destination', 'required'],
            ['destination', 'iataValidation'],
            ['destination', 'compare', 'compareAttribute' => 'origin', 'operator' => '!='],

            ['departure', 'required'],
            ['departure', 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function iataValidation($attribute): void
    {
        if (!$iata = Airports::findOne(['iata' => $this->$attribute])) {
            $this->addError($attribute, 'IATA (' . $this->$attribute . ') not found.');
            return;
        }
        $this->{$attribute . 'Label'} = $iata->getSelection();
    }
}

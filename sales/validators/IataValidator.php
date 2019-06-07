<?php

namespace sales\validators;

use common\models\Airport;
use yii\validators\ExistValidator;

class IataValidator extends ExistValidator
{
    public $targetClass = Airport::class;
    public $targetAttribute = 'iata';
    public $message = 'IATA ({value}) not found.';
}

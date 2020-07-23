<?php

namespace common\components\validators;

use common\models\Airports;
use yii\validators\ExistValidator;

class IataValidator extends ExistValidator
{
    public $targetClass = Airports::class;
    public $targetAttribute = 'iata';
    public $message = 'IATA ({value}) not found.';
}

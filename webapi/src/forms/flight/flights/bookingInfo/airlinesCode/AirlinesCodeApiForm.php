<?php

namespace  webapi\src\forms\flight\flights\bookingInfo\airlinesCode;

use yii\base\Model;

/**
 * Class AirlinesCodeApiForm
 *
 * @property $code
 * @property $airline
 * @property $recordLocator
 */
class AirlinesCodeApiForm extends Model
{
    public $code;
    public $airline;
    public $recordLocator;

    public function rules(): array
    {
        return [
            /* TODO:: required ? */
            [['code'], 'string', 'max' => 3],
            [['airline'], 'string', 'max' => 50],
            [['recordLocator'], 'string', 'max' => 255],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

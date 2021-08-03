<?php

namespace modules\flight\src\useCases\reprotectionCreate\form;

use common\components\validators\CheckJsonValidator;
use frontend\helpers\JsonHelper;
use yii\base\Model;

/**
 * Class ReprotectionCreateForm
 *
 * @property $booking_id
 * @property $is_automate
 * @property $flight_quote
 */
class ReprotectionCreateForm extends Model
{
    public $booking_id;
    public $is_automate;
    public $flight_quote;

    public function rules(): array
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id'], 'string', 'max' => 10],

            [['is_automate'], 'boolean'],
            [['is_automate'], 'default', 'value' => false],

            [['flight_quote'], CheckJsonValidator::class, 'skipOnEmpty' => true],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

<?php

namespace modules\flight\src\useCases\reprotectionCreate\form;

/**
 * Class ReprotectionGetForm
 * @package modules\flight\src\useCases\reprotectionCreate\form
 *
 * @property string $flight_product_quote_gid
 */
class ReprotectionGetForm extends \yii\base\Model
{
    public $flight_product_quote_gid;

    public function rules(): array
    {
        return [
            ['flight_product_quote_gid', 'required'],
            ['flight_product_quote_gid', 'string', 'max' => 32],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

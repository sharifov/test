<?php

namespace webapi\src\forms\payment\creditCard;

use yii\base\Model;

/**
 * Class StripeForm
 *
 * @property string $token_source
 */
class StripeForm extends Model
{
    public $token_source;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['token_source'], 'required'],

            [['token_source'], 'string', 'max' => 255],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

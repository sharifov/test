<?php

namespace modules\flight\src\useCases\api\productQuoteGet;

use common\components\validators\CheckJsonValidator;
use common\components\validators\IsArrayValidator;
use yii\base\Model;

/**
 * Class ProductQuoteGetForm
 *
 * @property $product_quote_gid
 * @property $with
 */
class ProductQuoteGetForm extends Model
{
    public const WITH_REPROTECTION = 'reprotection';
    public const WITH_LIST = [
        self::WITH_REPROTECTION,
    ];

    public $product_quote_gid;
    public $with;

    public function rules(): array
    {
        return [
            ['product_quote_gid', 'required'],
            ['product_quote_gid', 'string'],

            ['with', CheckJsonValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
            ['with', IsArrayValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
            ['with', 'validateWith', 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    public function validateWith(): void
    {
        foreach ($this->with as $value) {
            if (!in_array($value, self::WITH_LIST, true)) {
                $this->addError('with', 'One of Value is invalid');
                return;
            }
        }
    }

    public function withReprotection(): bool
    {
        if ($this->with) {
            return in_array(self::WITH_REPROTECTION, $this->with, true);
        }
        return false;
    }

    public function formName(): string
    {
        return '';
    }
}

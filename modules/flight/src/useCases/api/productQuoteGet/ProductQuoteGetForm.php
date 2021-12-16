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
    public const QUOTE_LIST = 'quote_list';
    public const WITH_LAST_CHANGE = 'last_change';

    public const WITH_LIST = [
        self::QUOTE_LIST,
        self::WITH_LAST_CHANGE,
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

    public function withQuoteList(): bool
    {
        if ($this->with) {
            return in_array(self::QUOTE_LIST, $this->with, true);
        }
        return false;
    }

    public function withLastChange(): bool
    {
        if ($this->with) {
            return in_array(self::WITH_LAST_CHANGE, $this->with, true);
        }
        return false;
    }

    public function formName(): string
    {
        return '';
    }
}

<?php

namespace modules\order\src\forms\api;

use modules\product\src\entities\productQuote\ProductQuote;
use yii\base\Model;

/**
 * Class OfferForm
 * @package modules\order\src\forms\api
 *
 * @property string $gid
 */
class ProductQuotesForm extends Model
{
    public string $gid = '';

    public function rules(): array
    {
        return [
            ['gid', 'required'],
            ['gid', 'string'],
            ['gid', 'exist', 'targetClass' => ProductQuote::class, 'targetAttribute' => 'pq_gid']
        ];
    }

    public function formName(): string
    {
        return 'productQuotes';
    }
}

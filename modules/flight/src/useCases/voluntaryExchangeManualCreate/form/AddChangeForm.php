<?php

namespace modules\flight\src\useCases\voluntaryExchangeManualCreate\form;

use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use sales\entities\cases\Cases;

/**
 * Class AddChangeForm
 */
class AddChangeForm extends \yii\base\Model
{
    public $case_id;
    public $origin_quote_id;
    public $type_id;
    public $order_id;

    public function rules(): array
    {
        return [
            [['case_id'], 'required'],
            [['case_id'], 'integer'],
            [['case_id'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            [['case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['case_id' => 'cs_id']],

            [['origin_quote_id'], 'required'],
            [['origin_quote_id'], 'integer'],
            [['origin_quote_id'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            [['origin_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['origin_quote_id' => 'pq_id']],

            [['type_id'], 'integer'],
            [['type_id'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true, 'skipOnError' => true],
            [['type_id'], 'in', 'range' => array_keys(ProductQuoteChange::TYPE_LIST)],
            [['type_id'], 'default', 'value' => ProductQuoteChange::TYPE_RE_PROTECTION],
            [['order_id'], 'safe'],
        ];
    }

    public function formName()
    {
        return '';
    }
}

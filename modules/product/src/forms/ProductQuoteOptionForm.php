<?php

namespace modules\product\src\forms;

use modules\product\src\entities\productOption\ProductOption;
use modules\product\src\entities\productQuote\ProductQuote;
use yii\base\Model;

/**
 * This is the form class for table "product_quote_option".
 *
 * @property int $pqo_id
 * @property int $pqo_product_quote_id
 * @property int|null $pqo_product_option_id
 * @property string $pqo_name
 * @property string|null $pqo_description
 * @property int|null $pqo_status_id
 * @property float|null $pqo_price
 * @property float|null $pqo_client_price
 * @property float|null $pqo_extra_markup
 */
class ProductQuoteOptionForm extends Model
{

    public $pqo_id;
    public $pqo_product_quote_id;
    public $pqo_product_option_id;
    public $pqo_name;
    public $pqo_description;
    public $pqo_status_id;
    public $pqo_price;
    public $pqo_client_price;
    public $pqo_extra_markup;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['pqo_product_quote_id', 'pqo_product_option_id', 'pqo_name'], 'required'],
            [['pqo_product_quote_id', 'pqo_product_option_id', 'pqo_status_id'], 'integer'],
            [['pqo_description'], 'string'],
            [['pqo_price', 'pqo_client_price', 'pqo_extra_markup'], 'number'],
            [['pqo_name'], 'string', 'max' => 50],
            [['pqo_product_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductOption::class, 'targetAttribute' => ['pqo_product_option_id' => 'po_id']],
            [['pqo_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqo_product_quote_id' => 'pq_id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'pqo_id' => 'ID',
            'pqo_product_quote_id' => 'Product Quote ID',
            'pqo_product_option_id' => 'Product Option ID',
            'pqo_name' => 'Name',
            'pqo_description' => 'Description',
            'pqo_status_id' => 'Status ID',
            'pqo_price' => 'Price',
            'pqo_client_price' => 'Client Price',
            'pqo_extra_markup' => 'Extra Markup',
        ];
    }
}

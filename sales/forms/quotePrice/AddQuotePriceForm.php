<?php

namespace sales\forms\quotePrice;

use common\models\Quote;
use yii\base\Model;

/**
 * @property int $quote_id
 * @property string $passenger_type
 * @property double $taxes
 * @property double $mark_up
 * @property double $extra_mark_up
 * @property double $service_fee
 * @property double $selling
 * @property double $net
 * @property double $fare
 */
class AddQuotePriceForm extends Model
{
    public $quote_id;
    public $passenger_type;
    public $taxes;
    public $mark_up;
    public $extra_mark_up;
    public $service_fee;
    public $selling;
    public $net;
    public $fare;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['quote_id'], 'integer'],
            [['selling', 'net', 'fare', 'taxes', 'mark_up', 'service_fee'], 'number'],
            [['extra_mark_up', 'service_fee'], 'number', 'min' => 0],

            [['passenger_type'], 'string', 'max' => 255],
            [['quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::class, 'targetAttribute' => ['quote_id' => 'id']],

            [['fare', 'taxes', 'net'], 'filter', 'filter' => 'intval'],

            [['selling', 'net'], 'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],

            ['fare', function ($attribute) {
                if (($this->net === 0 && $this->$attribute === 0) || $this->$attribute < 0) {
                    $this->addError($attribute, 'Fare must be greater than zero');
                }
            }],
            ['taxes', function ($attribute) {
                if (($this->net === 0 && $this->$attribute === 0) || $this->$attribute < 0) {
                    $this->addError($attribute, 'Taxes must be greater than zero');
                }
            }],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'quote_id' => 'Quote ID',
            'passenger_type' => 'Passenger Type',
            'selling' => 'Selling',
            'net' => 'Net',
            'fare' => 'Fare',
            'taxes' => 'Taxes',
            'mark_up' => 'Mark Up',
            'extra_mark_up' => 'Extra Mark Up',
        ];
    }
}

<?php

namespace sales\forms\segment;

use common\models\QuotePrice;
use yii\base\Model;

/**
 * @property string $type
 * @property int $piece
 * @property string $paxCode
 * @property string $segmentIata
 * @property string $maxWeight
 * @property string $maxSize
 * @property double|null $price
 * @property string $currency
 * @property array $baggageData
 */
class SegmentBaggageForm extends Model
{
    public $type;
    public $segmentIata;
    public $piece;
    public $paxCode;
    public $maxWeight;
    public $maxSize;
    public $price;
    public $currency;

    public $baggageData;

    public CONST TYPE_FREE = 'free';
    public CONST TYPE_PAID = 'paid';

    public CONST TYPE_LIST = [
        self::TYPE_FREE => 'Free',
        self::TYPE_PAID => 'Paid'
    ];

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['type', 'segmentIata', 'paxCode'], 'required'],

            [['type'], 'in', 'range' => array_keys(self::TYPE_LIST)],
            [['paxCode'], 'default', 'value' => QuotePrice::PASSENGER_ADULT],
            [['paxCode'], 'in', 'range' => array_keys(QuotePrice::PASSENGER_TYPE_LIST)],

            [['segmentIata'], 'string', 'length' => 6],
            [['maxWeight', 'maxSize'], 'string', 'max' => 100],
            [['price'], 'number'],
            [['piece'], 'integer'],

            [['currency'], 'default', 'value' => 'USD'],
            [['currency'], 'string', 'max' => 5],
        ];
    }
}

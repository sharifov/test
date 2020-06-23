<?php

namespace sales\forms\segment;

use common\models\QuotePrice;
use sales\services\parsingDump\BaggageService;
use yii\base\Model;

/**
 * @property string $type
 * @property int $piece
 * @property string $paxCode
 * @property string $segmentIata
 * @property string $weight
 * @property string $height
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
    public $weight; // maxWeight
    public $height; // maxSize
    public $price;
    public $currency;

    public $baggageData;

    /**
     * @param string|null $segmentIata
     * @param array $config
     */
    public function __construct(?string $segmentIata = null, $config = [])
	{
		$this->segmentIata = $segmentIata;
		parent::__construct($config);
	}

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function formName(): string
    {
        $formName = parent::formName();
        return $this->segmentIata ? $formName . '_' . $this->segmentIata : $formName;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['type', 'segmentIata', 'paxCode', 'piece'], 'required'],

            [['type'], 'in', 'range' => array_keys(BaggageService::TYPE_LIST)],
            [['paxCode'], 'default', 'value' => QuotePrice::PASSENGER_ADULT],
            [['paxCode'], 'in', 'range' => array_keys(QuotePrice::PASSENGER_TYPE_LIST)],

            [['segmentIata'], 'string', 'length' => 6],
            [['weight', 'height'], 'string', 'max' => 100],

            ['price', 'filter', 'filter' => static function ($value) {
                return BaggageService::prepareCost($value);
            }],
            [['price'], 'number'],

            [['piece'], 'integer'],

            [['currency'], 'default', 'value' => 'USD'],
            [['currency'], 'string', 'max' => 5],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array /* TODO::  */
    {
        return [];
    }
}

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
 * @property int $segmentId
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
    public $segmentId;

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
            [['type', 'segmentIata', 'piece'], 'required'],
            [['segmentId'], 'integer'],
            [['piece'], 'integer', 'min' => 1, 'max' => 99],

            [['type'], 'in', 'range' => array_keys(BaggageService::TYPE_LIST)],
            [['paxCode'], 'default', 'value' => QuotePrice::PASSENGER_ADULT],
            [['paxCode'], 'in', 'range' => array_keys(QuotePrice::PASSENGER_TYPE_LIST)],

            [['price'],'number','min' => 0.01, 'max' => 9999 , 'when' => function() {
                return $this->type === BaggageService::TYPE_PAID;
            }],
            ['price', 'required', 'when' => function () {
                return ($this->type === BaggageService::TYPE_PAID);
            }, 'skipOnError' => true],
            [['price'], function($attribute) {
                if ($this->type === BaggageService::TYPE_FREE && !in_array($this->$attribute, ['', 0], false)) {
                    $this->addError($attribute, 'Baggage should be free');
                }
            }],

            [['segmentIata'], 'string', 'length' => 6],
            [['weight', 'height'], 'string', 'max' => 100],

            [['currency'], 'default', 'value' => 'USD'],
            [['currency'], 'string', 'max' => 5],

            [['baggageData'], 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'type' => 'Type',
            'segmentIata' => 'Segment Iata',
            'piece' => 'Piece',
            'paxCode' => 'Pax Code',
            'price' => 'Cost',
            'weight' => 'Max Weight',
            'height' => 'Max Size',
            'currency' => 'Currency',
        ];
    }
}

<?php

namespace sales\forms\segment;

use common\models\QuotePrice;
use sales\services\parsingDump\BaggageService;
use yii\base\Model;

/**
 * @property $segment_iata
 * @property $segment_trip_key
 */
class SegmentTripForm extends Model
{
    public $segment_iata;
    public $segment_trip_key;

    /**
     * @param string|null $segmentIata
     * @param array $config
     */
    public function __construct(?string $segmentIata = null, $config = [])
    {
        $this->segment_iata = $segmentIata;
        parent::__construct($config);
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function formName(): string
    {
        $formName = parent::formName();
        return $this->segment_iata ? $formName . '_' . $this->segment_iata : $formName;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['segment_iata'], 'string', 'length' => 6],

            [['segment_trip_key'], 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'segment_iata' => 'Segment Iata',
            'segment_trip_key' => 'Trip key',
        ];
    }
}

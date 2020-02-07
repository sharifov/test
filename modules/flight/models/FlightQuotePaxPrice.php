<?php

namespace modules\flight\models;

use common\models\Currency;
use modules\flight\src\entities\flightQuotePaxPrice\serializer\FlightQuotePaxPriceSerializer;
use modules\flight\src\useCases\flightQuote\create\FlightQuotePaxPriceDTO;
use Yii;

/**
 * This is the model class for table "flight_quote_pax_price".
 *
 * @property int $qpp_id
 * @property int $qpp_flight_quote_id
 * @property int $qpp_flight_pax_code_id
 * @property float|null $qpp_fare
 * @property float|null $qpp_tax
 * @property float|null $qpp_system_mark_up
 * @property float|null $qpp_agent_mark_up
 * @property float|null $qpp_origin_fare
 * @property string|null $qpp_origin_currency
 * @property float|null $qpp_origin_tax
 * @property string|null $qpp_client_currency
 * @property float|null $qpp_client_fare
 * @property float|null $qpp_client_tax
 * @property string|null $qpp_created_dt
 * @property string|null $qpp_updated_dt
 * @property string|null $qpp_cnt
 *
 * @property Currency $qppClientCurrency
 * @property FlightQuote $qppFlightQuote
 * @property Currency $qppOriginCurrency
 */
class FlightQuotePaxPrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'flight_quote_pax_price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qpp_flight_quote_id', 'qpp_flight_pax_code_id'], 'required'],
            [['qpp_flight_quote_id', 'qpp_flight_pax_code_id'], 'integer'],
            [['qpp_fare', 'qpp_tax', 'qpp_system_mark_up', 'qpp_agent_mark_up', 'qpp_origin_fare', 'qpp_origin_tax', 'qpp_client_fare', 'qpp_client_tax'], 'number'],
            [['qpp_created_dt', 'qpp_updated_dt'], 'safe'],
            [['qpp_origin_currency', 'qpp_client_currency'], 'string', 'max' => 3],
            [['qpp_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['qpp_client_currency' => 'cur_code']],
            [['qpp_flight_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => FlightQuote::class, 'targetAttribute' => ['qpp_flight_quote_id' => 'fq_id']],
            [['qpp_origin_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['qpp_origin_currency' => 'cur_code']],

            ['qpp_cnt', 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qpp_id' => 'Qpp ID',
            'qpp_flight_quote_id' => 'Qpp Flight Quote ID',
            'qpp_flight_pax_code_id' => 'Qpp Flight Pax Code ID',
            'qpp_fare' => 'Qpp Fare',
            'qpp_tax' => 'Qpp Tax',
            'qpp_system_mark_up' => 'Qpp System Mark Up',
            'qpp_agent_mark_up' => 'Qpp Agent Mark Up',
            'qpp_origin_fare' => 'Qpp Origin Fare',
            'qpp_origin_currency' => 'Qpp Origin Currency',
            'qpp_origin_tax' => 'Qpp Origin Tax',
            'qpp_client_currency' => 'Qpp Client Currency',
            'qpp_client_fare' => 'Qpp Client Fare',
            'qpp_client_tax' => 'Qpp Client Tax',
            'qpp_created_dt' => 'Qpp Created Dt',
            'qpp_updated_dt' => 'Qpp Updated Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQppClientCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'qpp_client_currency']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQppFlightQuote()
    {
        return $this->hasOne(FlightQuote::class, ['fq_id' => 'qpp_flight_quote_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQppOriginCurrency()
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'qpp_origin_currency']);
    }

    /**
     * {@inheritdoc}
     * @return \modules\flight\models\query\FlightQuotePaxPriceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \modules\flight\models\query\FlightQuotePaxPriceQuery(static::class);
    }

	/**
	 * @param FlightQuotePaxPriceDTO $dto
	 * @return FlightQuotePaxPrice
	 */
    public static function create(FlightQuotePaxPriceDTO $dto): self
	{
		$paxPrice = new self();

		$paxPrice->qpp_flight_quote_id = $dto->flightQuoteId;
		$paxPrice->qpp_flight_pax_code_id = $dto->flightPaxCodeId;
		$paxPrice->qpp_cnt = $dto->cnt;
		$paxPrice->qpp_fare = $dto->fare;
		$paxPrice->qpp_tax = $dto->tax;
		$paxPrice->qpp_system_mark_up = $dto->systemMarkUp;
		$paxPrice->qpp_agent_mark_up = $dto->agentMarkUp;
		$paxPrice->qpp_origin_fare = $dto->originFare;
		$paxPrice->qpp_origin_currency = $dto->originCurrency;
		$paxPrice->qpp_origin_tax = $dto->originTax;
		$paxPrice->qpp_client_fare = $dto->clientFare;
		$paxPrice->qpp_client_currency = $dto->clientCurrency;
		$paxPrice->qpp_client_tax = $dto->clientTax;

		return $paxPrice;
	}

    public static function clone(FlightQuotePaxPrice $paxPrice, int $quoteId): self
    {
        $clone = new self();

        $clone->attributes = $paxPrice->attributes;

        $clone->qpp_id = null;
        $clone->qpp_flight_quote_id = $quoteId;

        return $clone;
	}

    public function serialize(): array
    {
        return (new FlightQuotePaxPriceSerializer($this))->getData();
	}
}

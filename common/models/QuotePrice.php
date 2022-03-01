<?php

namespace common\models;

use src\forms\quotePrice\AddQuotePriceForm;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quote_price".
 *
 * @property int $id
 * @property int $quote_id
 * @property string $passenger_type
 * @property double $taxes
 * @property double $mark_up
 * @property double $extra_mark_up
 * @property double $service_fee
 * @property string $created
 * @property string $updated
 * @property string $uid
 * @property double $qp_client_fare
 * @property double $qp_client_taxes
 * @property double $qp_client_markup
 * @property double $qp_client_extra_mark_up
 * @property double $qp_client_service_fee
 * @property double $qp_client_selling
 * @property double $qp_client_net
 *
 * @property string $oldParams
 *
 * @property Quote $quote
 *
 * @property double $selling
 * @property double $net
 * @property double $fare
 */
class QuotePrice extends \yii\db\ActiveRecord
{
    public const
        PASSENGER_ADULT = 'ADT',
        PASSENGER_CHILD = 'CHD',
        PASSENGER_INFANT = 'INF';

    public const PASSENGER_TYPE_LIST = [
        self::PASSENGER_ADULT => 'Adult',
        self::PASSENGER_CHILD => 'Child',
        self::PASSENGER_INFANT => 'Infant'
    ];

    public $oldParams;
    public int $defaultPrecision = 2;

    /**
     * @param array $attributes
     * @param int $quoteId
     * @return static
     */
    public static function clone(array $attributes, int $quoteId): self
    {
        $price = new self();
        $price->attributes = $attributes;
        $price->quote_id = $quoteId;
        $price->uid = uniqid(explode('.', $price->uid)[0] . '.');
        $price->toFloat();
        return $price;
    }

    public static function manualCreation(AddQuotePriceForm $form): self
    {
        $price = new self();
        $price->quote_id = $form->quote_id;
        $price->passenger_type = $form->passenger_type;
        $price->fare = $form->fare;
        $price->taxes = $form->taxes;
        $price->net = $form->net;
        $price->mark_up = $form->mark_up;
        $price->selling = $form->selling;
        $price->service_fee = $form->service_fee;
        return $price;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_price';
    }

    /**
     * @return bool
     */
    public function isAdult(): bool
    {
        return $this->passenger_type === self::PASSENGER_ADULT;
    }

    /**
     * @return bool
     */
    public function isChild(): bool
    {
        return $this->passenger_type === self::PASSENGER_CHILD;
    }

    /**
     * @return bool
     */
    public function isInfant(): bool
    {
        return $this->passenger_type === self::PASSENGER_INFANT;
    }


    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    public static function calculation(self &$model, $check_payment = true)
    {
        $model->oldAttributes = unserialize($model->oldParams);
        $model->oldParams = '';
        $model->toFloat();

        if ($model->oldAttributes['selling'] != $model->selling) {
            $model->mark_up = $model->mark_up + ($model->selling - $model->oldAttributes['selling']);
        } elseif ($model->oldAttributes['net'] != $model->net) {
            $model->fare = $model->fare + ($model->net - $model->oldAttributes['net']);
            $model->mark_up = $model->selling - $model->net;
            if ($model->fare < 0) {
                $model->taxes = $model->taxes + $model->fare;
                $model->fare = 0;
            }
            $model->selling = $model->net + $model->mark_up;
        } else {
            if ($model->fare >= $model->net) {
                $model->net = $model->fare + $model->taxes;
            } else {
                $model->taxes = $model->net - $model->fare;
            }
            $model->selling = $model->net + $model->mark_up + $model->extra_mark_up;
        }
        $model->selling = ($model->selling < 0)
            ? 0 : $model->selling;

        if (!$check_payment) {
            $model->service_fee = 0;
        } else {
            $model->service_fee = round($model->selling * (new Quote())->serviceFee, 2);
        }

        $model->selling += $model->service_fee;

        $model->roundAttributesValue();

        $model->oldParams = serialize($model->attributes);
    }

    /**
     * @param Quote|null $quote
     * @param bool $refresh
     * @return $this
     */
    public function calculatePrice(?Quote $quote = null, bool $refresh = false): QuotePrice
    {
        $this->oldAttributes = unserialize($this->oldParams, ['allowed_classes' => false]);
        $this->oldParams = '';
        $this->toFloat();

        if ($quote && !(bool) $quote->check_payment) {
            $serviceFee = 0;
        } else {
            $serviceFee = (new Quote())->serviceFee;
        }

        if ($this->oldAttributes['selling'] !== $this->selling || $refresh) {
            $this->mark_up = $this->selling / (1 + $serviceFee) - $this->net; // Selling Price/(1+SERVICE_FEE) - Net Price
        } elseif ($this->oldAttributes['fare'] !== $this->fare || $refresh) {
            $this->net = $this->fare + $this->taxes;
            $this->selling = ($this->fare + $this->taxes + $this->mark_up) * (1 + $serviceFee); // Selling Price = (Fare + Taxes + Mark-up)*(1+SERVICE_FEE)
        } elseif ($this->oldAttributes['taxes'] !== $this->taxes || $refresh) {
            $this->net = $this->fare + $this->taxes;
            $this->selling = ($this->fare + $this->taxes + $this->mark_up) * (1 + $serviceFee);
        } elseif ($this->oldAttributes['mark_up'] !== $this->mark_up || $refresh) {
            $this->selling = ($this->fare + $this->taxes + $this->mark_up) * (1 + $serviceFee);
        } else {
            $this->oldParams = serialize($this->attributes);
            return $this;
        }

        $this->roundAttributesValue();
        $this->oldParams = serialize($this->attributes);

        return $this;
    }

    public static function calculateProcessingFeeAmount(float $sellingPrice, float $serviceFeePercent): float
    {
        return round(($sellingPrice / (100 / 100 - $serviceFeePercent / 100)) - $sellingPrice, 2);
    }

    public function toFloat(&$attributes = null)
    {
        if ($attributes === null) {
            foreach ($this->attributes as $attr => $value) {
                if (in_array($attr, ['net', 'selling', 'extra_mark_up', 'mark_up', 'taxes', 'fare', 'service_fee'])) {
                    $this->$attr = (float)str_replace(',', '', $value);
                }
            }
        } else {
            foreach ($attributes as $attr => $value) {
                if (in_array($attr, ['net', 'selling', 'extra_mark_up', 'mark_up', 'taxes', 'fare', 'service_fee'])) {
                    $attributes[$attr] = (float)str_replace(',', '', $value);
                }
            }
        }
    }

    /**
     * @param int $precision
     */
    public function roundAttributesValue($precision = 2): void
    {
        foreach ($this->attributes as $attr => $value) {
            if (in_array($attr, ['net', 'selling', 'extra_mark_up', 'mark_up', 'taxes', 'fare', 'service_fee'])) {
                $this->$attr = $this->roundValue($value, $precision);
            }
        }
    }

    /**
     * @param $value
     * @param int|null $precision
     * @return false|float
     */
    public function roundValue($value, ?int $precision = null)
    {
        $precision = $precision ?? $this->defaultPrecision;
        return round((float) $value, $precision);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quote_id'], 'integer'],
            [['selling', 'net', 'fare', 'taxes', 'mark_up', 'service_fee'], 'number', 'max' => 99999],
            [['extra_mark_up'], 'number', 'min' => 0],
            [['taxes'],'number','min' => 0, 'when' => function ($model) {
                return $model->passenger_type !== self::PASSENGER_INFANT;
            }],
            [['created', 'updated', 'oldParams', 'uid'], 'safe'],
            [['passenger_type'], 'string', 'max' => 255],
            [['quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::class, 'targetAttribute' => ['quote_id' => 'id']],

            [[
                'qp_client_fare', 'qp_client_taxes', 'qp_client_markup', 'qp_client_net',
                'qp_client_extra_mark_up', 'qp_client_service_fee', 'qp_client_selling',
            ], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quote_id' => 'Quote ID',
            'passenger_type' => 'Passenger Type',
            'selling' => 'Selling',
            'net' => 'Net',
            'fare' => 'Fare',
            'taxes' => 'Taxes',
            'mark_up' => 'Mark Up',
            'extra_mark_up' => 'Extra Mark Up',
            'created' => 'Created',
            'updated' => 'Updated',
            'qp_client_fare' => 'Client fare',
            'qp_client_taxes' => 'Client taxes',
            'qp_client_markup' => 'Client markup',
            'qp_client_extra_mark_up' => 'Client extra markup',
            'qp_client_service_fee' => 'Client service fee',
            'qp_client_selling' => 'Client selling',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuote()
    {
        return $this->hasOne(Quote::class, ['id' => 'quote_id']);
    }

    public function afterFind()
    {
        $serviceFeePercent = $this->quote->getServiceFeePercent();
        //$this->oldParams = serialize($this->attributes);
        $this->net = $this->taxes + $this->fare;

        $this->selling = ($this->net + $this->mark_up + $this->extra_mark_up);
        if ($serviceFeePercent > 0) {
            $this->service_fee = self::calculateProcessingFeeAmount((float)$this->selling, (float)$serviceFeePercent);
            $this->selling += $this->service_fee;
        }
        $this->net = round($this->net, 2);
        $this->service_fee = round($this->service_fee, 2);
        $this->selling = round($this->selling, 2);

        parent::afterFind();
    }

    /*public function afterValidate()
    {
        $this->updated = date('Y-m-d H:i:s');

        if (empty($this->uid)) {
            $this->uid = uniqid('seller.');
        }

        parent::afterValidate();
    }*/


    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            //$this->updated = date('Y-m-d H:i:s');

            if (empty($this->uid)) {
                $this->uid = uniqid('seller.');
            }

            /*if ($insert) {


            }*/

            return true;
        }
        return false;
    }


    public function getData()
    {
        $data = [
            'fare' => $this->fare,
            'taxes' => $this->taxes,
            'mark_up' => $this->mark_up,
            'extra_mark_up' => $this->extra_mark_up,
            'service_fee' => 0,
            'selling' => 0,
            'net' => $this->fare + $this->taxes,
            'cnt' => 1
        ];

        $data['selling'] = $data['net'] + $data['mark_up'] + $data['extra_mark_up'];
        $service_fee_percent = $this->quote->getServiceFeePercent();
        $service_fee = ($service_fee_percent > 0) ? $data['selling'] * $service_fee_percent / 100 : 0;
        $data['selling'] += $service_fee;
        $data['selling'] = round($data['selling']);

        return $data;
    }

    public function createQPrice($paxType)
    {
        $this->passenger_type = $paxType;
        $this->selling = $this->net = $this->fare = $this->taxes = $this->mark_up = $this->extra_mark_up = 0;

        $this->service_fee = $this->quote ? round($this->selling * $this->quote->getServiceFee(), 2) : 0;
        $this->selling += $this->service_fee;

        $this->toFloat();
        $this->roundAttributesValue();
        $this->oldParams = serialize($this->attributes);
    }

    public function toMoney()
    {
        foreach ($this->attributes as $attr => $value) {
            if (in_array($attr, ['net', 'selling', 'extra_mark_up', 'mark_up', 'taxes', 'fare', 'service_fee'])) {
                $this->$attr = number_format($value, 2);
            }
        }
    }

    public function getPassengerTypeName($type = null): string
    {
        if ($type !== null) {
            return self::PASSENGER_TYPE_LIST[$type] ?? '-';
        }
        return self::PASSENGER_TYPE_LIST[$this->passenger_type] ?? '-';
    }
}

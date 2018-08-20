<?php

namespace common\models;

use common\models\local\LeadLogMessage;
use Yii;

/**
 * This is the model class for table "quote_price".
 *
 * @property int $id
 * @property int $quote_id
 * @property string $passenger_type
 * @property double $selling
 * @property double $net
 * @property double $fare
 * @property double $taxes
 * @property double $mark_up
 * @property double $extra_mark_up
 * @property string $created
 * @property string $updated
 * @property string $uid
 *
 * @property string $oldParams
 *
 * @property Quote $quote
 */
class QuotePrice extends \yii\db\ActiveRecord
{
    const
        PASSENGER_ADULT = 'ADT',
        PASSENGER_CHILD = 'CHD',
        PASSENGER_INFANT = 'INF';

    public $oldParams;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_price';
    }

    public static function calculation(self &$model)
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
        $model->roundValue();

        $model->oldParams = serialize($model->attributes);
    }

    public function toFloat(&$attributes = null)
    {
        if ($attributes === null) {
            foreach ($this->attributes as $attr => $value) {
                if (in_array($attr, ['net', 'selling', 'extra_mark_up', 'mark_up', 'taxes', 'fare'])) {
                    $this->$attr = (float)str_replace(',', '', $value);
                }
            }
        } else {
            foreach ($attributes as $attr => $value) {
                if (in_array($attr, ['net', 'selling', 'extra_mark_up', 'mark_up', 'taxes', 'fare'])) {
                    $attributes[$attr] = (float)str_replace(',', '', $value);
                }
            }
        }
    }

    public function roundValue($precision = 2)
    {
        foreach ($this->attributes as $attr => $value) {
            if (in_array($attr, ['net', 'selling', 'extra_mark_up', 'mark_up', 'taxes', 'fare'])) {
                $this->$attr = round($value, $precision);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quote_id'], 'integer'],
            [['selling', 'net', 'fare', 'taxes', 'mark_up', 'extra_mark_up'], 'number'],
            [['created', 'updated', 'oldParams', 'uid'], 'safe'],
            [['passenger_type'], 'string', 'max' => 255],
            [['quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::class, 'targetAttribute' => ['quote_id' => 'id']],
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
        $this->oldParams = serialize($this->attributes);

        parent::afterFind();
    }

    public function afterValidate()
    {
        $this->updated = date('Y-m-d H:i:s');

        if (empty($this->uid)) {
            $this->uid = uniqid('seller.');
        }

        parent::afterValidate();
    }

    public function createQPrice($paxType)
    {
        $this->passenger_type = $paxType;
        $this->selling = $this->net = $this->fare = $this->taxes = $this->mark_up = $this->extra_mark_up = 0;
        $this->toFloat();
        $this->roundValue();
        $this->oldParams = serialize($this->attributes);
    }

    public function toMoney()
    {
        foreach ($this->attributes as $attr => $value) {
            if (in_array($attr, ['net', 'selling', 'extra_mark_up', 'mark_up', 'taxes', 'fare'])) {
                $this->$attr = number_format($value, 2);
            }
        }
    }
}

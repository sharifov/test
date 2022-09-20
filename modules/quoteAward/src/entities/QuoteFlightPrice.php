<?php

namespace modules\quoteAward\src\entities;

use common\models\QuotePrice;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quote_flight_price".
 *
 * @property int $fqp_id
 * @property int $fqp_qp_id
 * @property int $fqp_qf_id
 * @property int $fqp_qfp_id
 * @property int $fqp_miles
 * @property float|null $fqp_ppm
 * @property string|null $fqp_created_dt
 * @property string|null $fqp_updated_dt
 *
 * @property QuoteFlight $quoteFlight
 * @property QuoteFlightProgram $quoteFlightProgram
 * @property QuotePrice $quotePrice
 */
class QuoteFlightPrice extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_flight_price';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fqp_qp_id', 'fqp_qf_id', 'fqp_qfp_id', 'fqp_miles'], 'required'],
            [['fqp_qp_id', 'fqp_qf_id', 'fqp_qfp_id', 'fqp_miles'], 'integer'],
            [['fqp_ppm'], 'number'],
            [['fqp_created_dt', 'fqp_updated_dt'], 'safe'],
            [['fqp_qf_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteFlight::className(), 'targetAttribute' => ['fqp_qf_id' => 'qf_id']],
            [['fqp_qfp_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteFlightProgram::className(), 'targetAttribute' => ['fqp_qfp_id' => 'gfp_id']],
            [['fqp_qp_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuotePrice::className(), 'targetAttribute' => ['fqp_qp_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fqp_created_dt', 'fqp_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['fqp_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fqp_id' => 'ID',
            'fqp_qp_id' => 'Quote Price ID',
            'fqp_qf_id' => 'Quote Flight ID',
            'fqp_qfp_id' => 'Quote Flight Program ID',
            'fqp_miles' => 'Miles',
            'fqp_ppm' => ' Ppm',
            'fqp_created_dt' => 'Created Dt',
            'fqp_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[QuoteFlight]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteFlight(): \yii\db\ActiveQuery
    {
        return $this->hasOne(QuoteFlight::className(), ['qf_id' => 'fqp_qf_id']);
    }

    /**
     * Gets query for [[QuoteFlightProgram]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuoteFlightProgram(): \yii\db\ActiveQuery
    {
        return $this->hasOne(QuoteFlightProgram::className(), ['gfp_id' => 'fqp_qfp_id']);
    }

    /**
     * Gets query for [[QuotePrice]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotePrice(): \yii\db\ActiveQuery
    {
        return $this->hasOne(QuotePrice::className(), ['id' => 'fqp_qp_id']);
    }
}

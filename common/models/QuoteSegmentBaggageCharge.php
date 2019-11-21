<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "quote_segment_baggage_charge".
 *
 * @property int $qsbc_id
 * @property string $qsbc_pax_code
 * @property int $qsbc_segment_id
 * @property int $qsbc_first_piece
 * @property int $qsbc_last_piece
 * @property double $qsbc_price
 * @property string $qsbc_currency
 * @property string $qsbc_max_weight
 * @property string $qsbc_max_size
 * @property string $qsbc_created_dt
 * @property string $qsbc_updated_dt
 * @property int $qsbc_updated_user_id
 *
 * @property QuoteSegment $qsbcSegment
 * @property Employee $qsbcUpdatedUser
 */
class QuoteSegmentBaggageCharge extends \yii\db\ActiveRecord
{

    /**
     * @param array $attributes
     * @param int $qsId
     * @return QuoteSegmentBaggageCharge
     */
    public static function clone(array $attributes, int $qsId): self
    {
        $baggageCharge = new self();
        $baggageCharge->attributes = $attributes;
        $baggageCharge->qsbc_segment_id = $qsId;
        return $baggageCharge;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_segment_baggage_charge';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsbc_segment_id', 'qsbc_first_piece', 'qsbc_last_piece', 'qsbc_updated_user_id'], 'integer'],
            [['qsbc_price'], 'number'],
            [['qsbc_created_dt', 'qsbc_updated_dt'], 'safe'],
            [['qsbc_pax_code'], 'string', 'max' => 3],
            [['qsbc_currency'], 'string', 'max' => 5],
            [['qsbc_max_weight', 'qsbc_max_size'], 'string', 'max' => 100],
            [['qsbc_segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteSegment::class, 'targetAttribute' => ['qsbc_segment_id' => 'qs_id']],
            [['qsbc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['qsbc_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qsbc_id' => 'Qsbc ID',
            'qsbc_pax_code' => 'Qsbc Pax Code',
            'qsbc_segment_id' => 'Qsbc Segment ID',
            'qsbc_first_piece' => 'Qsbc First Piece',
            'qsbc_last_piece' => 'Qsbc Last Piece',
            'qsbc_price' => 'Qsbc Price',
            'qsbc_currency' => 'Qsbc Currency',
            'qsbc_max_weight' => 'Qsbc Max Weight',
            'qsbc_max_size' => 'Qsbc Max Size',
            'qsbc_created_dt' => 'Qsbc Created Dt',
            'qsbc_updated_dt' => 'Qsbc Updated Dt',
            'qsbc_updated_user_id' => 'Qsbc Updated User ID',
        ];
    }

    public function getInfo()
    {
        $data = [];

        if(!empty($this->qsbc_price)){
            $data['price'] = $this->qsbc_price;
        }
        if(!empty($this->qsbc_currency)){
            $data['currency'] = $this->qsbc_currency;
        }
        if(!empty($this->qsbc_max_weight)){
            $data['maxWeight'] = $this->qsbc_max_weight;
        }
        if(!empty($this->qsbc_max_size)){
            $data['maxSize'] = $this->qsbc_max_size;
        }
        if(!empty($this->qsbc_first_piece)){
            $data['firstPiece'] = $this->qsbc_first_piece;
        }
        if(!empty($this->qsbc_last_piece)){
            $data['lastPiece'] = $this->qsbc_last_piece;
        }

        return $data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbcSegment()
    {
        return $this->hasOne(QuoteSegment::class, ['qs_id' => 'qsbc_segment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbcUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'qsbc_updated_user_id']);
    }
}

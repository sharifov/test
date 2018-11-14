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
            [['qsbc_segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteSegment::className(), 'targetAttribute' => ['qsbc_segment_id' => 'qs_id']],
            [['qsbc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['qsbc_updated_user_id' => 'id']],
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbcSegment()
    {
        return $this->hasOne(QuoteSegment::className(), ['qs_id' => 'qsbc_segment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbcUpdatedUser()
    {
        return $this->hasOne(Employee::className(), ['id' => 'qsbc_updated_user_id']);
    }
}

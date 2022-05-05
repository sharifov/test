<?php

namespace common\models;

use src\forms\segment\SegmentBaggageForm;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
            [['qsbc_segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteSegment::class, 'targetAttribute' => ['qsbc_segment_id' => 'qs_id']],
            [['qsbc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['qsbc_updated_user_id' => 'id']],

            [['qsbc_pax_code'], 'default', 'value' => Quote::PASSENGER_ADULT],
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
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['qsbc_created_dt', 'qsbc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['qsbc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

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

    public function getInfo()
    {
        $data = [];

        if (!empty($this->qsbc_price)) {
            $data['price'] = $this->qsbc_price;
        }
        if (!empty($this->qsbc_currency)) {
            $data['currency'] = $this->qsbc_currency;
        }
        if (!empty($this->qsbc_max_weight)) {
            $data['maxWeight'] = $this->qsbc_max_weight;
        }
        if (!empty($this->qsbc_max_size)) {
            $data['maxSize'] = $this->qsbc_max_size;
        }
        if (!empty($this->qsbc_first_piece)) {
            $data['firstPiece'] = $this->qsbc_first_piece;
        }
        if (!empty($this->qsbc_last_piece)) {
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

    /**
     * @param SegmentBaggageForm $form
     * @param int $firstPiece
     * @param int $lastPiece
     * @return static
     */
    public static function creationFromForm(SegmentBaggageForm $form, int $firstPiece = 1, int $lastPiece = 1): self
    {
        $item = new self();
        $item->qsbc_price = $form->price;
        $item->qsbc_pax_code = $form->paxCode;
        $item->qsbc_segment_id = $form->segmentId;
        $item->qsbc_first_piece = $firstPiece;
        $item->qsbc_last_piece = $lastPiece;
        $item->qsbc_max_size = $form->height;
        $item->qsbc_max_weight = $form->weight;
        $item->qsbc_currency = $form->currency;
        return $item;
    }

    public static function createFromSearch(array $baggageEntryCharge, string $paxCode): QuoteSegmentBaggageCharge
    {
        $baggageCharge = new self();
        $baggageCharge->qsbc_pax_code = $paxCode;
        $baggageCharge->qsbc_price = $baggageEntryCharge['price'] ?? null;
        $baggageCharge->qsbc_currency = $baggageEntryCharge['currency'] ?? null;
        $baggageCharge->qsbc_first_piece = $baggageEntryCharge['firstPiece'] ?? null;
        $baggageCharge->qsbc_last_piece = $baggageEntryCharge['lastPiece'] ?? null;
        $baggageCharge->qsbc_max_weight = $baggageEntryCharge['maxWeight'] ?? null;
        $baggageCharge->qsbc_max_size = $baggageEntryCharge['maxSize'] ?? null;
        return $baggageCharge;
    }
}

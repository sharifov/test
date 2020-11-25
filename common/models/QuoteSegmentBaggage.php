<?php

namespace common\models;

use sales\forms\segment\SegmentBaggageForm;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "quote_segment_baggage".
 *
 * @property int $qsb_id
 * @property string $qsb_pax_code
 * @property int $qsb_segment_id
 * @property string $qsb_airline_code
 * @property int $qsb_allow_pieces
 * @property int $qsb_allow_weight
 * @property string $qsb_allow_unit
 * @property string $qsb_allow_max_weight
 * @property string $qsb_allow_max_size
 * @property string $qsb_created_dt
 * @property string $qsb_updated_dt
 * @property int $qsb_updated_user_id
 * @property bool $qsb_carry_one
 *
 * @property QuoteSegment $qsbSegment
 * @property Employee $qsbUpdatedUser
 */
class QuoteSegmentBaggage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_segment_baggage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qsb_segment_id', 'qsb_allow_pieces', 'qsb_allow_weight', 'qsb_updated_user_id'], 'integer'],
            [['qsb_created_dt', 'qsb_updated_dt'], 'safe'],
            [['qsb_pax_code', 'qsb_airline_code'], 'string', 'max' => 3],
            [['qsb_allow_unit'], 'string', 'max' => 4],
            [['qsb_carry_one'], 'boolean'],
            [['qsb_carry_one'], 'default', 'value' => true],
            [['qsb_allow_max_weight', 'qsb_allow_max_size'], 'string', 'max' => 100],
            [['qsb_segment_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuoteSegment::class, 'targetAttribute' => ['qsb_segment_id' => 'qs_id']],
            [['qsb_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['qsb_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qsb_id' => 'Qsb ID',
            'qsb_pax_code' => 'Qsb Pax Code',
            'qsb_segment_id' => 'Qsb Segment ID',
            'qsb_airline_code' => 'Qsb Airline Code',
            'qsb_allow_pieces' => 'Qsb Allow Pieces',
            'qsb_allow_weight' => 'Qsb Allow Weight',
            'qsb_allow_unit' => 'Qsb Allow Unit',
            'qsb_allow_max_weight' => 'Qsb Allow Max Weight',
            'qsb_allow_max_size' => 'Qsb Allow Max Size',
            'qsb_created_dt' => 'Qsb Created Dt',
            'qsb_updated_dt' => 'Qsb Updated Dt',
            'qsb_updated_user_id' => 'Qsb Updated User ID',
            'qsb_carry_one' => 'Carry one',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['qsb_created_dt', 'qsb_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['qsb_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @param array $attributes
     * @param int $qsId
     * @return static
     */
    public static function clone(array $attributes, int $qsId): self
    {
        $baggage = new self();
        $baggage->attributes = $attributes;
        $baggage->qsb_segment_id = $qsId;
        return $baggage;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbSegment()
    {
        return $this->hasOne(QuoteSegment::class, ['qs_id' => 'qsb_segment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQsbUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'qsb_updated_user_id']);
    }

    public function getInfo()
    {
        $data = [];

        if (!empty($this->qsb_airline_code)) {
            $data['airlineCode'] = $this->qsb_airline_code;
        }
        if (!is_null($this->qsb_allow_pieces)) {
            $data['allowPieces'] = $this->qsb_allow_pieces;
        }
        if (!empty($this->qsb_allow_unit)) {
            $data['allowUnit'] = $this->qsb_allow_unit;
        }
        if (!empty($this->qsb_allow_weight)) {
            $data['allowWeight'] = $this->qsb_allow_weight;
        }
        if (!empty($this->qsb_allow_max_size)) {
            $data['allowMaxSize'] = $this->qsb_allow_max_size;
        }
        if (!empty($this->qsb_allow_max_weight)) {
            $data['allowMaxWeight'] = $this->qsb_allow_max_weight;
        }

        return $data;
    }

    /**
     * @param SegmentBaggageForm $form
     * @return QuoteSegmentBaggage
     */
    public static function creationFromForm(SegmentBaggageForm $form): QuoteSegmentBaggage
    {
        $item = new self();
        $item->qsb_pax_code = $form->paxCode;
        $item->qsb_segment_id = $form->segmentId;
        $item->qsb_allow_pieces = $form->piece;
        $item->qsb_allow_max_weight = $form->height;
        $item->qsb_allow_max_size = $form->weight;
        return $item;
    }
}

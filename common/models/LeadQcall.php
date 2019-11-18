<?php

namespace common\models;

use Faker\Provider\DateTime;
use sales\services\lead\qcall\Interval;
use Yii;

/**
 * This is the model class for table "lead_qcall".
 *
 * @property int $lqc_lead_id
 * @property string $lqc_dt_from
 * @property string $lqc_dt_to
 * @property int $lqc_weight
 * @property $lqc_created_dt
 * @property $lqc_call_from
 * @property DateTime $lqc_reservation_time
 *
 * @property Lead $lqcLead
 */
class LeadQcall extends \yii\db\ActiveRecord
{

    /**
     * @param int $leadId
     * @param int $weight
     * @param Interval $interval
     * @param string|null $callFrom
     * @return LeadQcall
     */
    public static function create(
        int $leadId,
        int $weight,
        Interval $interval,
        ?string $callFrom
    ): self
    {
        $lq = new static();
        $lq->lqc_lead_id = $leadId;
        $lq->lqc_weight = $weight;
        $lq->lqc_dt_from = $interval->fromFormat();
        $lq->lqc_dt_to = $interval->toFormat();
        $lq->lqc_call_from = $callFrom;
        $lq->lqc_created_dt = date('Y-m-d H:i:s');
        return $lq;
    }

    /**
     * @param Interval $interval
     */
    public function updateInterval(Interval $interval): void
    {
        $this->lqc_dt_from = $interval->fromFormat();
        $this->lqc_dt_to = $interval->toFormat();
    }

    /**
     * @param string|null $callFrom
     */
    public function updateCallFrom(?string $callFrom): void
    {
        if ($callFrom === null) {
            return;
        }
        $this->lqc_call_from = $callFrom;
    }

    public function removeCallFrom(): void
    {
        $this->lqc_call_from = null;
    }

    /**
     * @param \DateTime $dt
     */
    public function updateReservationTime(\DateTime $dt): void
    {
        $this->lqc_reservation_time = $dt->format('Y-m-d H:i:s');
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_qcall';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lqc_lead_id', 'lqc_dt_from', 'lqc_dt_to'], 'required'],
            [['lqc_lead_id', 'lqc_weight'], 'integer'],
            [['lqc_dt_from', 'lqc_dt_to'], 'safe'],
            [['lqc_lead_id'], 'unique'],
            [['lqc_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lqc_lead_id' => 'id']],
            ['lqc_created_dt', 'string'],
            ['lqc_call_from', 'string'],
            ['lqc_reservation_time', 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lqc_lead_id' => 'Lead ID',
            'lqc_dt_from' => 'Date Time From',
            'lqc_dt_to' => 'Date Time To',
            'lqc_weight' => 'Weight',
            'lqc_created_dt' => 'Created',
            'lqc_call_from' => 'Call from',
            'lqc_reservation_time' => 'Reservation time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLqcLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'lqc_lead_id']);
    }

    /**
     * {@inheritdoc}
     * @return LeadQcallQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LeadQcallQuery(get_called_class());
    }
}

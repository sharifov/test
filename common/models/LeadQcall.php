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
 * @property string $lqc_reservation_time
 * @property int $lqc_reservation_user_id
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
     * @param int $userId
     */
    public function reservation(\DateTime $dt, int $userId): void
    {
        $this->lqc_reservation_time = $dt->format('Y-m-d H:i:s');
        $this->lqc_reservation_user_id = $userId;
    }

    /**
     * @param \DateTime $dt
     */
    public function updateReservationTime(\DateTime $dt): void
    {
        $this->lqc_reservation_time = $dt->format('Y-m-d H:i:s');
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isReservationUser(int $userId): bool
    {
        return $this->lqc_reservation_user_id === $userId;
    }

    /**
     * @return bool
     */
    public function isReserved(): bool
    {
        return $this->lqc_reservation_time !== null && strtotime(date('Y-m-d H:i:s')) < strtotime($this->lqc_reservation_time);
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function isReservedByUser(int $userId): bool
    {
        return $this->isReservationUser($userId) && $this->isReserved();
    }

    /**
     * @param int $leadId
     * @return bool
     */
    public function isEqual(int $leadId): bool
    {
        return $this->lqc_lead_id === $leadId;
    }

    /**
     * @param $weight
     * @param $from
     * @param $to
     * @param $created
     */
    public function multipleUpdate($weight, $from, $to, $created): void
    {
        if ($weight) {
            $this->lqc_weight = $weight;
        }
        if ($from) {
            $this->lqc_dt_from = $from;
        }
        if ($to) {
            $this->lqc_dt_to = $to;
        }
        if ($created) {
            $this->lqc_created_dt = $created;
        }
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
            ['lqc_reservation_user_id', 'integer'],
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

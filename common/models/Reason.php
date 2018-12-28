<?php

namespace common\models;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "reasons".
 *
 * @property int $id
 * @property int $employee_id
 * @property int $lead_id
 * @property string $reason
 * @property string $created
 *
 * @property Employee $employee
 * @property Lead $lead
 */
class Reason extends \yii\db\ActiveRecord
{
    public $returnToQueue;
    public $queue;
    public $other;
    public $duplicateLeadId;


    public const STATUS_REASON_LIST = [
        Lead::STATUS_TRASH => [
            1 => 'Purchased elsewhere',
            2 => 'Duplicate',
            3 => 'Travel dates passed',
            4 => 'Invalid phone number',
            5 => 'Canceled trip',
            6 => 'Test',
            0 => 'Other'
        ],
        Lead::STATUS_REJECT => [
            1 => 'Purchased elsewhere',
            2 => 'Flight date > 10 months',
            3 => 'Not interested',
            4 => 'Duplicate',
            5 => 'Too late',
            6 => 'Test',
            0 => 'Other'
        ],
        Lead::STATUS_FOLLOW_UP => [
            1 => 'Proper Follow Up Done',
            2 => "Didn't get in touch",
            0 => 'Other'
        ],
        Lead::STATUS_PROCESSING => [
            1 => 'N/A',
            2 => 'No Available',
            3 => 'Voice Mail Send',
            4 => 'Will call back',
            5 => 'Waiting the option',
            0 => 'Other'
        ],
        Lead::STATUS_ON_HOLD => [
            0 => 'Other'
        ],
        Lead::STATUS_SNOOZE => [
            1 => 'Travelling dates > 12 months',
            2 => 'Not ready to buy now',
            0 => 'Other'
        ],
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reasons';
    }

    /**
     * @param int $status_id
     * @param int $reason_id
     * @return string
     */
    public static function getReasonByStatus($status_id = 0, $reason_id = 0): string
    {
        return self::STATUS_REASON_LIST[$status_id][$reason_id] ?? '-';
    }

    /**
     * @param int $status_id
     * @return array
     */
    public static function getReasonListByStatus($status_id = 0): array
    {
        return self::STATUS_REASON_LIST[$status_id] ?? [];
    }

    /**
     * @param null $queue
     * @return array
     */
    public static function getReason($queue = null)
    {
        if (in_array($queue, ['trash', 'reject'])) {
            return [
                'Purchased elsewhere' => 'Purchased elsewhere',
                'Duplicate' => 'Duplicate',
                'Travel dates passed' => 'Travel dates passed',
                'Invalid phone number' => 'Invalid phone number',
                'Canceled trip' => 'Canceled trip',
                'Test' => 'Test',
                'Other' => 'Other'
            ];

        } elseif ($queue == 'follow-up') {
            return [
                'Proper Follow Up Done' => 'Proper Follow Up Done',
                'Didn\'t get in touch' => 'Didn\'t get in touch',
                'Other' => 'Other'
            ];
        } elseif ($queue == 'processing') {
            return [
                'N/A' => 'N/A',
                'No Available' => 'No Available',
                'Voice Mail Send' => 'Voice Mail Send',
                'Will call back' => 'Will call back',
                'Waiting the option' => 'Waiting the option',
                'Other' => 'Other'
            ];
        } elseif ($queue == 'processing-over') {
            return [
                'Client asked for assistance' => 'Client asked for assistance',
                'Duplicate' => 'Duplicate',
                'I am original agent' => 'I am original agent',
                'Other' => 'Other'
            ];
        } elseif ($queue == 'snooze') {
            return [
                'Travelling dates > 12 months' => 'Travelling dates > 12 months',
                'Not ready to buy now' => 'Not ready to buy now',
                'Other' => 'Other'
            ];
        }




        return [
            'Other' => 'Other'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['reason', 'required'],
            [['employee_id', 'lead_id'], 'integer'],
            [['created', 'queue', 'returnToQueue', 'other', 'duplicateLeadId'], 'safe'],
            [['reason'], 'string', 'max' => 255],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Employee ID',
            'lead_id' => 'Lead ID',
            'reason' => 'Reason',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'lead_id']);
    }

    public function afterValidate()
    {
        if ($this->other) {
            $this->reason = sprintf('%s: %s', $this->reason, $this->other);
        }

        if ($this->duplicateLeadId) {

            $aHref = Html::a($this->duplicateLeadId, [
                'lead/view',
                'id' => $this->duplicateLeadId
            ], ['data-pjax' => 0]);

            $this->reason = sprintf('%s: %s',
                $this->reason,
                $aHref
            );
        }
    }
}

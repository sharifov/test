<?php

namespace common\models;

use Yii;

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

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reasons';
    }

    public static function getReason($queue = null)
    {
        if (in_array($queue, ['trash', 'reject'])) {
            return [
                'Purchased elsewhere' => 'Purchased elsewhere',
                'Flight date > 10 months' => 'Flight date > 10 months',
                'Not interested' => 'Not interested',
                'Duplicate' => 'Duplicate',
                'Too late' => 'Too late',
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
            [['created', 'queue', 'returnToQueue', 'other'], 'safe'],
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
        if (!empty($this->other)) {
            $this->reason = sprintf('%s: %s', $this->reason, $this->other);
        }
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lead_qcall".
 *
 * @property int $lqc_lead_id
 * @property string $lqc_dt_from
 * @property string $lqc_dt_to
 * @property int $lqc_weight
 *
 * @property Lead $lqcLead
 */
class LeadQcall extends \yii\db\ActiveRecord
{
    public $attempts;
    public $deadline;

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

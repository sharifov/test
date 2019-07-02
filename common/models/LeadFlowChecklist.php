<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%lead_flow_checklist}}".
 *
 * @property int $lfc_lf_id
 * @property int $lfc_lc_type_id
 * @property int $lfc_lc_user_id
 *
 * @property Employee $user
 * @property LeadFlow $leadFlow
 * @property LeadChecklistType $checklistType
 */
class LeadFlowChecklist extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%lead_flow_checklist}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['lfc_lf_id', 'lfc_lc_type_id'], 'required'],
            [['lfc_lf_id', 'lfc_lc_type_id', 'lfc_lc_user_id'], 'integer'],
            [['lfc_lf_id', 'lfc_lc_type_id'], 'unique', 'targetAttribute' => ['lfc_lf_id', 'lfc_lc_type_id']],
            [['lfc_lc_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lfc_lc_user_id' => 'id']],
            [['lfc_lf_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadFlow::class, 'targetAttribute' => ['lfc_lf_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'lfc_lf_id' => 'Lfc Lf ID',
            'lfc_lc_type_id' => 'Lfc Lc Type ID',
            'lfc_lc_user_id' => 'Lfc Lc User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'lfc_lc_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadFlow(): ActiveQuery
    {
        return $this->hasOne(LeadFlow::class, ['id' => 'lfc_lf_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChecklistType(): ActiveQuery
    {
        return $this->hasOne(LeadChecklistType::class, ['lct_id' => 'lfc_lc_type_id']);
    }
}

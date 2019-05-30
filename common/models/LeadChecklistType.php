<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lead_checklist_type".
 *
 * @property int $lct_id
 * @property string $lct_key
 * @property string $lct_name
 * @property string $lct_description
 * @property int $lct_enabled
 * @property int $lct_sort_order
 * @property string $lct_updated_dt
 * @property int $lct_updated_user_id
 *
 * @property LeadChecklist[] $leadChecklists
 * @property Employee $lctUpdatedUser
 */
class LeadChecklistType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_checklist_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lct_key', 'lct_name'], 'required'],
            [['lct_enabled', 'lct_sort_order', 'lct_updated_user_id'], 'integer'],
            [['lct_updated_dt'], 'safe'],
            [['lct_key'], 'string', 'max' => 50],
            [['lct_name'], 'string', 'max' => 255],
            [['lct_description'], 'string', 'max' => 500],
            [['lct_key'], 'unique'],
            [['lct_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lct_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lct_id' => 'Lct ID',
            'lct_key' => 'Lct Key',
            'lct_name' => 'Lct Name',
            'lct_description' => 'Lct Description',
            'lct_enabled' => 'Lct Enabled',
            'lct_sort_order' => 'Lct Sort Order',
            'lct_updated_dt' => 'Lct Updated Dt',
            'lct_updated_user_id' => 'Lct Updated User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadChecklists()
    {
        return $this->hasMany(LeadChecklist::class, ['lc_type_id' => 'lct_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLctUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'lct_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return LeadChecklistTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LeadChecklistTypeQuery(get_called_class());
    }
}

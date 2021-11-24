<?php

namespace common\models;

/**
 * This is the model class for table "sms_template_type_project".
 *
 * @property int $sttp_stp_id
 * @property int $sttp_project_id
 *
 * @property Project $sttpProject
 * @property SmsTemplateType $sttpStp
 */
class SmsTemplateTypeProject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms_template_type_project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sttp_stp_id', 'sttp_project_id'], 'required'],
            [['sttp_stp_id', 'sttp_project_id'], 'integer'],
            [['sttp_stp_id', 'sttp_project_id'], 'unique', 'targetAttribute' => ['sttp_stp_id', 'sttp_project_id']],
            [['sttp_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['sttp_project_id' => 'id']],
            [['sttp_stp_id'], 'exist', 'skipOnError' => true, 'targetClass' => SmsTemplateType::class, 'targetAttribute' => ['sttp_stp_id' => 'stp_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sttp_stp_id' => 'Sttp Stp ID',
            'sttp_project_id' => 'Project',
        ];
    }

    /**
     * Gets query for [[SttpProject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttpProject()
    {
        return $this->hasOne(Project::class, ['id' => 'sttp_project_id']);
    }

    /**
     * Gets query for [[SttpStp]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttpStp()
    {
        return $this->hasOne(SmsTemplateType::class, ['stp_id' => 'sttp_stp_id']);
    }
}

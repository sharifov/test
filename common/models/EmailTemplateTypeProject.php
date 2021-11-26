<?php

namespace common\models;

/**
 * This is the model class for table "email_template_type_project".
 *
 * @property int $ettp_etp_id
 * @property int $ettp_project_id
 *
 * @property EmailTemplateType $ettpEtp
 * @property Project $ettpProject
 */
class EmailTemplateTypeProject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_template_type_project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ettp_etp_id', 'ettp_project_id'], 'required'],
            [['ettp_etp_id', 'ettp_project_id'], 'integer'],
            [['ettp_etp_id', 'ettp_project_id'], 'unique', 'targetAttribute' => ['ettp_etp_id', 'ettp_project_id']],
            [['ettp_etp_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['ettp_etp_id' => 'etp_id']],
            [['ettp_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['ettp_project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ettp_etp_id' => 'Ettp Etp ID',
            'ettp_project_id' => 'Project',
        ];
    }

    /**
     * Gets query for [[EttpEtp]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEttpEtp()
    {
        return $this->hasOne(EmailTemplateType::class, ['etp_id' => 'ettp_etp_id']);
    }

    /**
     * Gets query for [[EttpProject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEttpProject()
    {
        return $this->hasOne(Project::class, ['id' => 'ettp_project_id']);
    }
}

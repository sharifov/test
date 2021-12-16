<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sms_template_type_department".
 *
 * @property int $sttd_stp_id
 * @property int $sttd_department_id
 *
 * @property Department $sttdDepartment
 * @property SmsTemplateType $sttdStp
 */
class SmsTemplateTypeDepartment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms_template_type_department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sttd_stp_id', 'sttd_department_id'], 'required'],
            [['sttd_stp_id', 'sttd_department_id'], 'integer'],
            [['sttd_stp_id', 'sttd_department_id'], 'unique', 'targetAttribute' => ['sttd_stp_id', 'sttd_department_id']],
            [['sttd_department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['sttd_department_id' => 'dep_id']],
            [['sttd_stp_id'], 'exist', 'skipOnError' => true, 'targetClass' => SmsTemplateType::class, 'targetAttribute' => ['sttd_stp_id' => 'stp_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sttd_stp_id' => 'Sttd Stp ID',
            'sttd_department_id' => 'Departments',
        ];
    }

    /**
     * Gets query for [[SttdDepartment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttdDepartment()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'sttd_department_id']);
    }

    /**
     * Gets query for [[SttdStp]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSttdStp()
    {
        return $this->hasOne(SmsTemplateType::class, ['stp_id' => 'sttd_stp_id']);
    }
}

<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "email_template_type_department".
 *
 * @property int $ettd_etp_id
 * @property int $ettd_department_id
 *
 * @property Department $ettdDepartment
 * @property EmailTemplateType $ettdEtt
 */
class EmailTemplateTypeDepartment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_template_type_department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ettd_etp_id', 'ettd_department_id'], 'required'],
            [['ettd_etp_id', 'ettd_department_id'], 'integer'],
            [['ettd_etp_id', 'ettd_department_id'], 'unique', 'targetAttribute' => ['ettd_etp_id', 'ettd_department_id']],
            [['ettd_department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['ettd_department_id' => 'dep_id']],
            [['ettd_etp_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmailTemplateType::class, 'targetAttribute' => ['ettd_etp_id' => 'etp_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ettd_etp_id' => 'Ettd Ett ID',
            'ettd_department_id' => 'Departments',
        ];
    }

    /**
     * Gets query for [[EttdDepartment]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEttdDepartment()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'ettd_department_id']);
    }

    /**
     * Gets query for [[EttdEtt]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEttdEtt()
    {
        return $this->hasOne(EmailTemplateType::class, ['etp_id' => 'ettd_etp_id']);
    }
}

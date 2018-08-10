<?php

namespace common\models;

use Yii;
use borales\extensions\phoneInput\PhoneInputValidator;

/**
 * This is the model class for table "employee_contact_info".
 *
 * @property int $id
 * @property int $employee_id
 * @property int $project_id
 * @property string $email_user
 * @property string $email_pass
 * @property string $direct_line
 * @property string $created
 * @property string $updated
 *
 * @property Employee $employee
 * @property Project $project
 */
class EmployeeContactInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee_contact_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'project_id'], 'integer'],
            [['created', 'updated'], 'safe'],
            [['email_user', 'email_pass', 'direct_line'], 'string', 'max' => 255],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['employee_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
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
            'project_id' => 'Project ID',
            'email_user' => 'Email User',
            'email_pass' => 'Email Pass',
            'direct_line' => 'Direct Line',
            'created' => 'Created',
            'updated' => 'Updated',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    public function afterValidate()
    {
        parent::afterValidate();

        if (empty($this->email_user)) {
            $this->addError('email_user', 'can`t be blank.');
        }

        if (!empty($this->email_user) && empty($this->email_pass)) {
            $this->addError('email_pass', 'can`t be blank.');
        }

        if (empty($this->direct_line)) {
            $this->addError('direct_line', 'can`t be blank.');
        } else {
            $validator = new PhoneInputValidator();
            if (!$validator->validate($this->direct_line)) {
                $this->addError('direct_line', 'is invalid.');
            }
        }
    }

    public function needSave()
    {
        return (!empty($this->email_user) || !empty($this->email_pass) || !empty($this->direct_line));
    }
}

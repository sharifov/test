<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "department_phone_project".
 *
 * @property int $dpp_id
 * @property int $dpp_dep_id
 * @property int $dpp_project_id
 * @property string $dpp_phone_number
 * @property int $dpp_source_id
 * @property array $dpp_params
 * @property int $dpp_ivr_enable
 * @property int $dpp_enable
 * @property int $dpp_updated_user_id
 * @property string $dpp_updated_dt
 *
 * @property Department $dppDep
 * @property Project $dppProject
 * @property Sources $dppSource
 * @property Employee $dppUpdatedUser
 */
class DepartmentPhoneProject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department_phone_project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dpp_project_id', 'dpp_phone_number'], 'required'],
            [['dpp_dep_id', 'dpp_project_id', 'dpp_source_id', 'dpp_ivr_enable', 'dpp_enable', 'dpp_updated_user_id'], 'integer'],
            [['dpp_phone_number'], 'unique'],
            [['dpp_params', 'dpp_updated_dt'], 'safe'],
            [['dpp_phone_number'], 'string', 'max' => 18],
            [['dpp_dep_id', 'dpp_project_id', 'dpp_phone_number'], 'unique', 'targetAttribute' => ['dpp_dep_id', 'dpp_project_id', 'dpp_phone_number']],
            [['dpp_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['dpp_dep_id' => 'dep_id']],
            [['dpp_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['dpp_project_id' => 'id']],
            [['dpp_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sources::class, 'targetAttribute' => ['dpp_source_id' => 'id']],
            [['dpp_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['dpp_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dpp_id' => 'ID',
            'dpp_phone_number' => 'Phone Number',
            'dpp_dep_id' => 'Department',
            'dpp_project_id' => 'Project',
            'dpp_source_id' => 'Source',
            'dpp_params' => 'Params',
            'dpp_ivr_enable' => 'IVR Enable',
            'dpp_enable' => 'Enable',
            'dpp_updated_user_id' => 'Updated User',
            'dpp_updated_dt' => 'Updated Date',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['dpp_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['dpp_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'dpp_updated_user_id',
                'updatedByAttribute' => 'dpp_updated_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDppDep()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'dpp_dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDppProject()
    {
        return $this->hasOne(Project::class, ['id' => 'dpp_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDppSource()
    {
        return $this->hasOne(Sources::class, ['id' => 'dpp_source_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDppUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'dpp_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return DepartmentPhoneProjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DepartmentPhoneProjectQuery(get_called_class());
    }
}

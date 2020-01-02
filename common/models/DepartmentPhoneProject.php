<?php

namespace common\models;

use common\models\query\DepartmentPhoneProjectQuery;
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
 * @property bool $dpp_ivr_enable
 * @property bool $dpp_enable
 * @property int $dpp_updated_user_id
 * @property string $dpp_updated_dt
 * @property bool $dpp_redial
 * @property string $dpp_description
 * @property int $dpp_default
 *
 * @property array $user_group_list
 *
 * @property Department $dppDep
 * @property Project $dppProject
 * @property Sources $dppSource
 * @property Employee $dppUpdatedUser
 * @property DepartmentPhoneProjectUserGroup[] $departmentPhoneProjectUserGroups
 * @property UserGroup[] $dugUgs
 */
class DepartmentPhoneProject extends \yii\db\ActiveRecord
{

    public $user_group_list = [];

    public const DPP_DEFAULT_TRUE = 1;

    public const DEP_DEFAULT_TRUE = 1;

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
            [['dpp_dep_id', 'dpp_project_id', 'dpp_source_id', 'dpp_updated_user_id', 'dpp_default'], 'integer'],
            [['dpp_phone_number'], 'unique'],
            [['dpp_ivr_enable', 'dpp_enable'], 'boolean'],
            [['dpp_params', 'dpp_updated_dt', 'user_group_list'], 'safe'],
            [['dpp_phone_number'], 'string', 'max' => 18],
            [['dpp_dep_id', 'dpp_project_id', 'dpp_phone_number'], 'unique', 'targetAttribute' => ['dpp_dep_id', 'dpp_project_id', 'dpp_phone_number']],
            [['dpp_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['dpp_dep_id' => 'dep_id']],
            [['dpp_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['dpp_project_id' => 'id']],
            [['dpp_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sources::class, 'targetAttribute' => ['dpp_source_id' => 'id']],
            [['dpp_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['dpp_updated_user_id' => 'id']],

            ['dpp_redial', 'boolean'],
            ['dpp_description', 'string', 'max' => 255],
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
            'dpp_redial' => 'Redial phone',
            'dpp_description' => 'Description',
			'dpp_default' => 'Default'
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
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentPhoneProjectUserGroups()
    {
        return $this->hasMany(DepartmentPhoneProjectUserGroup::class, ['dug_dpp_id' => 'dpp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDugUgs()
    {
        return $this->hasMany(UserGroup::class, ['ug_id' => 'dug_ug_id'])->viaTable('department_phone_project_user_group', ['dug_dpp_id' => 'dpp_id']);
    }

    /**
     * {@inheritdoc}
     * @return DepartmentPhoneProjectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DepartmentPhoneProjectQuery(static::class);
    }
}

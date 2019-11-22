<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "department_email_project".
 *
 * @property int $dep_id
 * @property string $dep_email
 * @property int $dep_project_id
 * @property int $dep_dep_id
 * @property int $dep_source_id
 * @property int $dep_enable
 * @property string $dep_description
 * @property int $dep_updated_user_id
 * @property string $dep_updated_dt
 *
 * @property array $user_group_list
 *
 * @property Department $depDep
 * @property Project $depProject
 * @property Sources $depSource
 * @property Employee $depUpdatedUser
 * @property DepartmentEmailProjectUserGroup[] $departmentEmailProjectUserGroups
 * @property UserGroup[] $dugUgs
 */
class DepartmentEmailProject extends \yii\db\ActiveRecord
{
	public $user_group_list = [];

	/**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department_email_project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dep_email', 'dep_project_id'], 'required'],
            [['dep_project_id', 'dep_dep_id', 'dep_source_id', 'dep_enable', 'dep_updated_user_id'], 'integer'],
            [['dep_updated_dt'], 'safe'],
            [['dep_email'], 'string', 'max' => 18],
            [['dep_description'], 'string', 'max' => 255],
            [['dep_email'], 'unique'],
			[['user_group_list'], 'safe'],
			[['dep_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['dep_dep_id' => 'dep_id']],
            [['dep_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['dep_project_id' => 'id']],
            [['dep_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sources::class, 'targetAttribute' => ['dep_source_id' => 'id']],
            [['dep_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['dep_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dep_id' => 'ID',
            'dep_email' => 'Email',
            'dep_project_id' => 'Project',
            'dep_dep_id' => 'Department',
            'dep_source_id' => 'Source',
            'dep_enable' => 'Enable',
            'dep_description' => 'Description',
            'dep_updated_user_id' => 'Updated User',
            'dep_updated_dt' => 'When Updated',
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
					ActiveRecord::EVENT_BEFORE_INSERT => ['dep_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['dep_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'dep_updated_user_id',
				'updatedByAttribute' => 'dep_updated_user_id',
			],
		];
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepDep()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'dep_dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepProject()
    {
        return $this->hasOne(Project::class, ['id' => 'dep_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepSource()
    {
        return $this->hasOne(Sources::class, ['id' => 'dep_source_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'dep_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentEmailProjectUserGroups()
    {
        return $this->hasMany(DepartmentEmailProjectUserGroup::class, ['dug_dep_id' => 'dep_id']);
    }

	/**
	 * @return \yii\db\ActiveQuery
	 * @throws \yii\base\InvalidConfigException
	 */
    public function getDugUgs()
    {
        return $this->hasMany(UserGroup::class, ['ug_id' => 'dug_ug_id'])->viaTable('department_email_project_user_group', ['dug_dep_id' => 'dep_id']);
    }
}

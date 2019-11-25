<?php

namespace common\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "department".
 *
 * @property int $dep_id
 * @property string $dep_key
 * @property string $dep_name
 * @property int $dep_updated_user_id
 * @property string $dep_updated_dt
 *
 * @property Call[] $calls
 * @property Employee $depUpdatedUser
 * @property DepartmentPhoneProject[] $departmentPhoneProjects
 * @property Lead[] $leads
 * @property UserDepartment[] $userDepartments
 */
class Department extends \yii\db\ActiveRecord
{

    public const DEPARTMENT_SALES       = 1;
    public const DEPARTMENT_EXCHANGE    = 2;
    public const DEPARTMENT_SUPPORT     = 3;

    public const DEPARTMENT_LIST = [
        self::DEPARTMENT_SALES      => 'Sales',
        self::DEPARTMENT_EXCHANGE   => 'Exchange',
        self::DEPARTMENT_SUPPORT    => 'Support',
    ];

    /**
     * @return bool
     */
    public function isSales(): bool
    {
        return $this->dep_id === self::DEPARTMENT_SALES;
    }

    /**
     * @return bool
     */
    public function isExchange(): bool
    {
        return $this->dep_id === self::DEPARTMENT_EXCHANGE;
    }

    /**
     * @return bool
     */
    public function isSupport(): bool
    {
        return $this->dep_id === self::DEPARTMENT_SUPPORT;
    }

    /**
     * @param int $depId
     * @return string
     */
    public static function getName(int $depId): string
    {
        return self::DEPARTMENT_LIST[$depId] ?? 'Undefined';
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dep_id', 'dep_name'], 'required'],
            [['dep_id', 'dep_updated_user_id'], 'integer'],
            [['dep_updated_dt'], 'safe'],
            [['dep_key', 'dep_name'], 'string', 'max' => 20],
            [['dep_id'], 'unique'],
            [['dep_key'], 'unique'],
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
            'dep_key' => 'Key',
            'dep_name' => 'Name',
            'dep_updated_user_id' => 'Updated User',
            'dep_updated_dt' => 'Updated Date',
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
    public function getCalls()
    {
        return $this->hasMany(Call::class, ['c_dep_id' => 'dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentPhoneProjects()
    {
        return $this->hasMany(DepartmentPhoneProject::class, ['dpp_dep_id' => 'dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDepartments()
    {
        return $this->hasMany(UserDepartment::class, ['ud_dep_id' => 'dep_id']);
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
    public function getLeads()
    {
        return $this->hasMany(Lead::class, ['l_dep_id' => 'dep_id']);
    }

    /**
     * {@inheritdoc}
     * @return DepartmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DepartmentQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        $data = self::find()->orderBy(['dep_id' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'dep_id', 'dep_name');
    }
}

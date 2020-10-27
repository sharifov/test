<?php

namespace common\models;

use common\models\query\DepartmentPhoneProjectQuery;
use sales\model\phoneList\entity\PhoneList;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "department_phone_project".
 *
 * @property int $dpp_id
 * @property int|null $dpp_dep_id
 * @property int $dpp_project_id
 * @property string|null $dpp_phone_number
 * @property int|null $dpp_source_id
 * @property array $dpp_params
 * @property bool $dpp_ivr_enable
 * @property bool $dpp_enable
 * @property int|null $dpp_updated_user_id
 * @property string|null $dpp_updated_dt
 * @property bool $dpp_redial
 * @property string|null $dpp_description
 * @property int|null $dpp_default
 * @property bool $dpp_show_on_site
 * @property int|null $dpp_phone_list_id
 * @property string|null $dpp_language_id
 * @property int|null $dpp_allow_transfer
 *
 * @property array $user_group_list
 *
 * @property DepartmentPhoneProjectUserGroup[] $departmentPhoneProjectUserGroups
 * @property Department $dppDep
 * @property Language $dppLanguage
 * @property Project $dppProject
 * @property Sources $dppSource
 * @property Employee $dppUpdatedUser
 * @property UserGroup[] $dugUgs
 * @property PhoneList $phoneList
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
            ['dpp_project_id', 'required'],

//            ['dpp_phone_number', 'required'],
//            ['dpp_phone_number', 'unique'],
//            ['dpp_phone_number', 'string', 'max' => 18],
//            [['dpp_dep_id', 'dpp_project_id', 'dpp_phone_number'], 'unique', 'targetAttribute' => ['dpp_dep_id', 'dpp_project_id', 'dpp_phone_number']],

            [['dpp_dep_id', 'dpp_project_id', 'dpp_source_id', 'dpp_updated_user_id'], 'integer'],

            ['dpp_default', 'boolean'],

            [['dpp_ivr_enable', 'dpp_enable'], 'boolean'],
            [['dpp_params', 'dpp_updated_dt', 'user_group_list'], 'safe'],


            [['dpp_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['dpp_dep_id' => 'dep_id']],
            [['dpp_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['dpp_project_id' => 'id']],
            [['dpp_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sources::class, 'targetAttribute' => ['dpp_source_id' => 'id']],
            [['dpp_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['dpp_updated_user_id' => 'id']],

            ['dpp_redial', 'boolean'],
            ['dpp_allow_transfer', 'boolean'],
            [['dpp_phone_number'], 'string', 'max' => 18],
            ['dpp_description', 'string', 'max' => 255],
            [['dpp_language_id'], 'default', 'value' => null],
            [['dpp_language_id'], 'string', 'max' => 5],
            ['dpp_show_on_site', 'boolean'],

            ['dpp_phone_list_id', 'required'],
            ['dpp_phone_list_id', 'integer'],
            ['dpp_phone_list_id', 'unique'],
            [['dpp_phone_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => PhoneList::class, 'targetAttribute' => ['dpp_phone_list_id' => 'pl_id']],
            [['dpp_language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['dpp_language_id' => 'language_id']],

            [['dpp_dep_id', 'dpp_project_id', 'dpp_phone_list_id'], 'unique', 'targetAttribute' => ['dpp_dep_id', 'dpp_project_id', 'dpp_phone_list_id']],
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
            'dpp_default' => 'Default',
            'dpp_show_on_site' => 'Show on site',
            'dpp_phone_list_id' => 'Phone List',
            'phoneList.pl_phone_number' => 'Phone List',
            'dpp_language_id' => 'Language ID',
            'dpp_allow_transfer' => 'Allow transfer',
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
     * @return ActiveQuery
     */
    public function getDppDep(): ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 'dpp_dep_id']);
    }

    /**
     * Gets query for [[DppLanguage]].
     *
     * @return ActiveQuery
     */
    public function getDppLanguage(): ActiveQuery
    {
        return $this->hasOne(Language::class, ['language_id' => 'dpp_language_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDppProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'dpp_project_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDppSource(): ActiveQuery
    {
        return $this->hasOne(Sources::class, ['id' => 'dpp_source_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDppUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'dpp_updated_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDepartmentPhoneProjectUserGroups(): ActiveQuery
    {
        return $this->hasMany(DepartmentPhoneProjectUserGroup::class, ['dug_dpp_id' => 'dpp_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDugUgs(): ActiveQuery
    {
        return $this->hasMany(UserGroup::class, ['ug_id' => 'dug_ug_id'])->viaTable('department_phone_project_user_group', ['dug_dpp_id' => 'dpp_id']);
    }

    /**
     * @param bool $onlyEnabled
     * @return string|null
     */
    public function getPhone(bool $onlyEnabled = false): ?string
    {
        if (!$this->phoneList) {
            return null;
        }
        if ($onlyEnabled) {
            if ($this->phoneList->pl_phone_number) {
                return $this->phoneList->pl_phone_number;
            }
            return null;
        }
        return $this->phoneList->pl_phone_number;
    }

    /**
     * Gets query for [[PhoneList]].
     *
     * @return ActiveQuery
     */
    public function getPhoneList(): ActiveQuery
    {
        return $this->hasOne(PhoneList::class, ['pl_id' => 'dpp_phone_list_id']);
    }

    /**
     * @return DepartmentPhoneProjectQuery
     */
    public static function find(): DepartmentPhoneProjectQuery
    {
        return new DepartmentPhoneProjectQuery(static::class);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->dpp_enable;
    }
}

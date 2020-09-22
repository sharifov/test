<?php

namespace sales\model\phoneLine\phoneLine\entity;

use common\models\Department;
use common\models\Employee;
use common\models\Language;
use common\models\Project;
use common\models\UserGroup;
use sales\model\phoneLine\phoneLinePhoneNumber\entity\PhoneLinePhoneNumber;
use sales\model\phoneLine\phoneLineUserAssign\entity\PhoneLineUserAssign;
use sales\model\phoneLine\phoneLineUserGroup\entity\PhoneLineUserGroup;
use sales\model\userVoiceMail\entity\UserVoiceMail;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_line".
 *
 * @property int $line_id
 * @property string|null $line_name
 * @property int $line_project_id
 * @property int|null $line_dep_id
 * @property string|null $line_language_id
 * @property string|null $line_settings_json
 * @property int|null $line_personal_user_id
 * @property int|null $line_uvm_id
 * @property int|null $line_allow_in
 * @property int|null $line_allow_out
 * @property int|null $line_enabled
 * @property int|null $line_created_user_id
 * @property int|null $line_updated_user_id
 * @property string|null $line_created_dt
 * @property string|null $line_updated_dt
 *
 * @property Employee $lineCreatedUser
 * @property Department $lineDep
 * @property Employee $linePersonalUser
 * @property Project $lineProject
 * @property Employee $lineUpdatedUser
 * @property UserVoiceMail $lineUvm
 * @property PhoneLinePhoneNumber[] $phoneLinePhoneNumbers
 * @property PhoneLineUserAssign[] $phoneLineUserAssigns
 * @property PhoneLineUserGroup[] $phoneLineUserGroups
 * @property UserGroup[] $plugUgs
 * @property Employee[] $plusUsers
 */
class PhoneLine extends \yii\db\ActiveRecord
{
	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['line_created_dt', 'line_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['line_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
			],
		];
	}

    public function rules(): array
    {
        return [
            ['line_allow_in', 'integer'],

            ['line_allow_out', 'integer'],

            ['line_created_dt', 'safe'],

            ['line_created_user_id', 'integer'],
            ['line_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['line_created_user_id' => 'id']],

            ['line_dep_id', 'integer'],
            ['line_dep_id', 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['line_dep_id' => 'dep_id']],

            ['line_enabled', 'integer'],

            ['line_language_id', 'string', 'max' => 5],
			['line_language_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Language::class, 'targetAttribute' => ['line_language_id' => 'language_id']],

            ['line_name', 'string', 'max' => 100],

            ['line_personal_user_id', 'integer'],
            ['line_personal_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['line_personal_user_id' => 'id']],

            ['line_project_id', 'required'],
            ['line_project_id', 'integer'],
            ['line_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['line_project_id' => 'id']],

            ['line_settings_json', 'safe'],

            ['line_updated_dt', 'safe'],

            ['line_updated_user_id', 'integer'],
            ['line_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['line_updated_user_id' => 'id']],

            ['line_uvm_id', 'integer'],
            ['line_uvm_id', 'exist', 'skipOnError' => true, 'targetClass' => UserVoiceMail::class, 'targetAttribute' => ['line_uvm_id' => 'uvm_id']],
        ];
    }

    public function getLineCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'line_created_user_id']);
    }

    public function getLineDep(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 'line_dep_id']);
    }

    public function getLinePersonalUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'line_personal_user_id']);
    }

    public function getLineProject(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'line_project_id']);
    }

    public function getLineUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'line_updated_user_id']);
    }

    public function getLineUvm(): \yii\db\ActiveQuery
    {
        return $this->hasOne(UserVoiceMail::class, ['uvm_id' => 'line_uvm_id']);
    }

    public function getPhoneLinePhoneNumbers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(PhoneLinePhoneNumber::class, ['plpn_line_id' => 'line_id']);
    }

    public function getPhoneLineUserAssigns(): \yii\db\ActiveQuery
    {
        return $this->hasMany(PhoneLineUserAssign::class, ['plus_line_id' => 'line_id']);
    }

    public function getPhoneLineUserGroups(): \yii\db\ActiveQuery
    {
        return $this->hasMany(PhoneLineUserGroup::class, ['plug_line_id' => 'line_id']);
    }

    public function getPlugUgs(): \yii\db\ActiveQuery
    {
        return $this->hasMany(UserGroup::class, ['ug_id' => 'plug_ug_id'])->viaTable('phone_line_user_group', ['plug_line_id' => 'line_id']);
    }

    public function getPlusUsers(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Employee::class, ['id' => 'plus_user_id'])->viaTable('phone_line_user_assign', ['plus_line_id' => 'line_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'line_id' => 'ID',
            'line_name' => 'Name',
            'line_project_id' => 'Project ID',
            'line_dep_id' => 'Dep ID',
            'line_language_id' => 'Language ID',
            'line_settings_json' => 'Settings Json',
            'line_personal_user_id' => 'Personal User ID',
            'line_uvm_id' => 'Uvm ID',
            'line_allow_in' => 'Allow In',
            'line_allow_out' => 'Allow Out',
            'line_enabled' => 'Enabled',
            'line_created_user_id' => 'Created User ID',
            'line_updated_user_id' => 'Updated User ID',
            'line_created_dt' => 'Created Dt',
            'line_updated_dt' => 'Updated Dt',
        ];
    }

    public static function tableName(): string
    {
        return 'phone_line';
    }

    /**
     * @param bool|null $enabled
     * @return array
     */
    public static function getList(?bool $enabled = null): array
    {
        $query = self::find()->select(['line_name', 'line_id'])->orderBy(['line_name' => SORT_ASC]);

        if ($enabled !== null) {
            $query->where(['line_enabled' => $enabled]);
        }

        return $query->indexBy('line_id')->asArray()->column();
    }
}

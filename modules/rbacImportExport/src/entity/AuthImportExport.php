<?php

namespace modules\rbacImportExport\src\entity;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use modules\rbacImportExport\src\useCase\export\RbacExportDataDTO;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "auth_import_export".
 *
 * @property int $aie_id
 * @property int|null $aie_type
 * @property int|null $aie_cnt_roles
 * @property int|null $aie_cnt_permissions
 * @property int|null $aie_cnt_rules
 * @property int|null $aie_cnt_childs
 * @property string|null $aie_file_name
 * @property int|null $aie_file_size
 * @property string|null $aie_created_dt
 * @property int|null $aie_user_id
 * @property string|null $aie_data_json
 *
 * @property Employee $aieUser
 */
class AuthImportExport extends \yii\db\ActiveRecord
{
	public const TYPE_EXPORT = 1;
	public const TYPE_IMPORT = 2;

	public const TYPE_LIST = [
		self::TYPE_IMPORT => 'Import',
		self::TYPE_EXPORT => 'Export',
	];

	public const SECTION_USERS = 1;
	public const SECTION_PERMISSIONS = 2;
	public const SECTION_CHILD = 3;
	public const SECTION_GENERAL_RULES = 4;
	public const SECTION_GENERAL_PERMISSION = 5;


	public const SECTION_LIST = [
		self::SECTION_GENERAL_RULES => 'General Rules',
		self::SECTION_GENERAL_PERMISSION => 'General Permissions',
		self::SECTION_USERS => 'Users to Roles Assignment',
		self::SECTION_PERMISSIONS => 'Permissions to Roles Assignment',
		self::SECTION_CHILD => 'Child to Role Assignment',
	];

	/**
	 * @return array
	 */
	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'value' => date('Y-m-d H:i:s'), //new Expression('NOW()')
				'createdAtAttribute' => 'aie_created_dt',
				'updatedAtAttribute' => null

			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'aie_user_id',
				'updatedByAttribute' => null
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_import_export';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['aie_type', 'aie_cnt_roles', 'aie_cnt_permissions', 'aie_cnt_rules', 'aie_cnt_childs', 'aie_file_size', 'aie_user_id'], 'integer'],
            [['aie_created_dt', 'aie_data_json'], 'safe'],
            [['aie_file_name'], 'string', 'max' => 255],
            [['aie_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['aie_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'aie_id' => 'ID',
            'aie_type' => 'Type',
            'aie_cnt_roles' => 'Cnt Roles',
            'aie_cnt_permissions' => 'Cnt Permissions',
            'aie_cnt_rules' => 'Cnt Rules',
            'aie_cnt_childs' => 'Cnt Childs',
            'aie_file_name' => 'File Name',
            'aie_file_size' => 'File Size',
            'aie_created_dt' => 'Created Dt',
            'aie_user_id' => 'User',
            'aie_data_json' => 'Data Json',
        ];
    }

    /**
     * Gets query for [[AieUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getAieUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'aie_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }

    public static function getTypeList(): array
	{
		return self::TYPE_LIST;
	}

	public function getTypeName(): string
	{
		return self::getTypeList()[$this->aie_type];
	}

	public static function getSectionList(): array
	{
		return self::SECTION_LIST;
	}

	public static function create(RbacExportDataDTO $dto): self
	{
		$newRow = new self();

		$newRow->aie_type = $dto->type;
		$newRow->aie_cnt_roles = $dto->cntRoles;
		$newRow->aie_cnt_permissions = $dto->cntPermissions;
		$newRow->aie_cnt_rules = $dto->cntRules;
		$newRow->aie_cnt_childs = $dto->cntChild;
		$newRow->aie_file_name = $dto->fileName;
		$newRow->aie_file_size = $dto->fileSize;
		$newRow->aie_data_json = json_encode($dto->exportData);

		return $newRow;
	}
}

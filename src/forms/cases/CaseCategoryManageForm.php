<?php

namespace src\forms\cases;

use common\models\Department;
use src\entities\cases\CaseCategory;
use yii\base\Model;

class CaseCategoryManageForm extends Model
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';
    public const SCENARIO_INDEX = 'index';

    public ?string $parentCategoryId = '';
    public ?int $cc_id = null;
    public ?string $cc_key = null;
    public ?string $cc_name = null;
    public ?int $cc_dep_id = null;
    public ?int $cc_system = null;
    public ?bool $cc_enabled = null;
    public ?bool $cc_allow_to_select = null;
    public ?int $cc_lft = null;
    public ?int $cc_rgt = null;
    public ?int $cc_depth = null;
    public ?int $cc_tree = null;

    /**
     * Map attributes from Form Model to Active Record
     * @param CaseCategory $case
     * @return void
     */
    public function mapAttributesToModel(CaseCategory $case): void
    {
        $case->cc_key = $this->cc_key;
        $case->cc_name = $this->cc_name;
        $case->cc_dep_id = $this->cc_dep_id;
        $case->cc_allow_to_select = $this->cc_allow_to_select;
        $case->cc_system = $this->cc_system;
        $case->cc_enabled = $this->cc_enabled;
        if (!$case->cc_tree) {
            $case->cc_tree = $this->cc_tree ?? 0;
        }
    }

    /**
     * Map attributes from Active Record to Form Model
     * @param CaseCategory $case
     * @return void
     */
    public function mapAttributesFromModel(CaseCategory $case): void
    {
        $this->cc_key = $case->cc_key;
        $this->cc_id = $case->cc_id;
        $this->cc_name = $case->cc_name;
        $this->cc_dep_id = $case->cc_dep_id;
        $this->cc_allow_to_select = $case->cc_allow_to_select;
        $this->cc_system = $case->cc_system;
        $this->cc_enabled = $case->cc_enabled;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['cc_key', 'default', 'value' => null],
            ['cc_key', 'string', 'max' => 50],
            ['cc_key', 'filter', 'filter' => 'strtolower', 'skipOnEmpty' => true],
            [
                'cc_key',
                'match',
                'pattern' => '/^[a-z0-9_]+$/',
                'message' => 'Key can only contain alphanumeric characters, underscores.'
            ],
            [
                'cc_key',
                'unique',
                'targetClass' => CaseCategory::class,
                'targetAttribute' => ['cc_key' => 'cc_key'],
                'on' => self::SCENARIO_CREATE
            ],
            [
                'cc_name',
                'required',
                'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]
            ],
            ['cc_name', 'string', 'max' => 255],
            [
                'cc_name',
                'unique',
                'targetClass' => CaseCategory::class,
                'targetAttribute' => ['cc_name' => 'cc_name'],
                'on' => self::SCENARIO_CREATE
            ],

            ['cc_dep_id', 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            ['cc_dep_id', 'integer'],
            [['parentCategoryId'], 'string'],
            [
                'cc_dep_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => Department::class,
                'targetAttribute' => ['cc_dep_id' => 'dep_id'],
                'on' => self::SCENARIO_CREATE
            ],

            [['cc_system', 'cc_enabled'], 'boolean'],
            ['cc_enabled', 'default', 'value' => true],
            [['cc_lft', 'cc_rgt', 'cc_depth', 'cc_tree'], 'integer'],
            [['cc_lft', 'cc_rgt', 'cc_depth', 'cc_tree', 'parentCategoryId'], 'safe'],
            ['cc_allow_to_select', 'boolean'],
        ];
    }

    public function scenarios(): array
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['cc_key', 'cc_name', 'cc_dep_id', 'cc_system', 'cc_enabled', 'cc_lft', 'cc_rgt', 'cc_depth', 'cc_tree', 'parentCategoryId', 'cc_allow_to_select'];
        $scenarios[self::SCENARIO_UPDATE] = ['cc_key', 'cc_name', 'cc_dep_id', 'cc_system', 'cc_enabled', 'cc_lft', 'cc_rgt', 'cc_depth', 'cc_tree', 'parentCategoryId', 'cc_allow_to_select'];
        $scenarios[self::SCENARIO_INDEX] = ['cc_key', 'cc_name', 'cc_dep_id', 'cc_system', 'cc_enabled', 'cc_lft', 'cc_rgt', 'cc_depth', 'cc_tree', 'parentCategoryId', 'cc_allow_to_select'];
        return $scenarios;
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cc_id' => 'ID',
            'cc_key' => 'Key',
            'cc_name' => 'Name',
            'dep.dep_name' => 'Department',
            'cc_dep_id' => 'Department',
            'cc_system' => 'System',
            'cc_created_dt' => 'Created Dt',
            'cc_updated_dt' => 'Updated Dt',
            'cc_updated_user_id' => 'Updated User ID',
            'cc_enabled' => 'Enabled',
            'cc_allow_to_select' => 'Allow to select',
            'parentCategoryId' => 'Parent Category',
        ];
    }
}

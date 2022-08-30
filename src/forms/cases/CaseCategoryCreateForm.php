<?php

namespace src\forms\cases;

use common\models\Department;
use src\entities\cases\CaseCategory;
use src\entities\cases\CasesCategoryQuery;
use yii\base\Model;

class CaseCategoryCreateForm extends Model
{
    public ?string $parentCategoryId = null;
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

    public static function getDb()
    {
        return \Yii::$app->get('db');
    }

    public static function find()
    {
        return new CasesCategoryQuery(static::class);
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%case_category}}';
    }

    /**
     * Map attributes from Model to Active Record
     * @param CaseCategory $case
     * @return void
     */
    public function mapAttributes(CaseCategory $case): void
    {
        $case->cc_name = $this->cc_name;
        $case->cc_dep_id = $this->cc_dep_id;
        $case->cc_allow_to_select = $this->cc_allow_to_select;
        //todo something wrong here with setting default value to tree id before InsertAfter logic in
        //vendor/creocoder/yii2-nested-sets/src/NestedSetsBehavior.php
        $case->cc_tree = $this->cc_tree ?? 0;
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
            ['cc_key', 'unique'],
            ['cc_name', 'required'],
            ['cc_name', 'string', 'max' => 255],
            ['cc_name', 'unique'],

            ['cc_dep_id', 'required'],
            ['cc_dep_id', 'integer'],
            [
                'cc_dep_id',
                'exist',
                'skipOnError' => true,
                'targetClass' => Department::class,
                'targetAttribute' => ['cc_dep_id' => 'dep_id']
            ],

            [['cc_system', 'cc_enabled'], 'boolean'],
            ['cc_enabled', 'default', 'value' => true],
            [['cc_lft', 'cc_rgt', 'cc_depth', 'cc_tree'], 'integer'],
            [['cc_lft', 'cc_rgt', 'cc_depth', 'cc_tree', 'parentCategoryId'], 'safe'],
            ['cc_allow_to_select', 'boolean'],
        ];
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

<?php

namespace src\entities\cases;

use common\models\Department;
use common\models\Employee;
use creocoder\nestedsets\NestedSetsBehavior;
use kartik\tree\models\TreeTrait;
use src\behaviors\SlugBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class CaseCategory
 *
 * @property int $cc_id
 * @property string $cc_key
 * @property string $cc_name
 * @property int $cc_dep_id
 * @property int $cc_system
 * @property string $cc_created_dt
 * @property string $cc_updated_dt
 * @property int $cc_updated_user_id
 * @property bool $cc_enabled
 * @property bool $cc_allow_to_select;
 * @property int $cc_tree;
 * @property Cases[] $cases
 * @property Department $dep
 * @property Employee $updatedUser
 * @method NestedSetsBehavior makeRoot()
 * @method NestedSetsBehavior parents($depth = null)
 * @method NestedSetsBehavior appendTo($node, $runValidation = true, $attributes = null)
 *
 */
class CaseCategory extends ActiveRecord
{
    /**
     * @return array
     */
    use TreeTrait;

    public $encodeNodeNames = false;
    public $purifyNodeIcons = false;
    public function isDisabled()
    {
        return false;
    }
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cc_created_dt', 'cc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'updatedByAttribute' => 'cc_updated_user_id',
                'createdByAttribute' => 'cc_updated_user_id',
            ],
            'slugBehavior' => [
                'class' => SlugBehavior::class,
                'donorColumn' => 'cc_name',
                'targetColumn' => 'cc_key',
            ],
            'tree' => [
                'class' => NestedSetsBehavior::class,
                'leftAttribute' => 'cc_lft',
                'rightAttribute' => 'cc_rgt',
                'depthAttribute' => 'cc_depth',
                'treeAttribute' => 'cc_tree',
            ],
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
            'cc_lft' => 'Left Key',
            'cc_rgt' => 'Right Key',
            'cc_depth' => 'Depth Level',
            'cc_tree' => 'Tree ID',
        ];
    }
    /**
     * @return ActiveQuery
     */
    public function getCases(): ActiveQuery
    {
        return $this->hasMany(Cases::class, ['cs_category_id' => 'cc_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDep(): ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 'cc_dep_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cc_updated_user_id']);
    }

    /**
     * @param array|null $departments
     * @param bool|null $enabled
     * @return array
     */
    public static function getList(?array $departments = null, ?bool $enabled = null): array
    {
        $conditions = [];

        if ($departments) {
            $conditions['cc_dep_id'] = $departments;
        }
        if ($enabled) {
            $conditions['cc_enabled'] = $enabled;
        }

        $data = self::find()->select(['cc_name', 'cc_dep_id', 'cc_id', 'cc_enabled'])
            ->andWhere($conditions)
            ->indexBy('cc_id')
            ->orderBy(['cc_created_dt' => SORT_ASC])
            ->asArray()
            ->column();
        return $data;
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%case_category}}';
    }

    public static function find()
    {
        return new CasesCategoryQuery(static::class);
    }

    /**
     * @param int|null $categoryId
     * @return false|int|string|null
     */
    public static function getKey(?int $categoryId)
    {
        return self::find()->select(['cc_key'])->where(['cc_id' => $categoryId])->limit(1)->scalar();
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function findNestedSets()
    {
        return new CasesNestedSetsCategoryQuery(static::class);
    }
}

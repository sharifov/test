<?php

namespace src\entities\cases;

use common\models\Department;
use common\models\Employee;
use kartik\daterange\DateRangeBehavior;
use src\behaviors\SlugBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
 *
 * @property Cases[] $cases
 * @property Department $dep
 * @property Employee $updatedUser
 */
class CaseCategory extends ActiveRecord
{
    /**
     * @return array
     */
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
        ];
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
            ['cc_key', 'match', 'pattern' => '/^[a-z0-9_]+$/', 'message' =>  'Key can only contain alphanumeric characters, underscores.'],
            ['cc_key', 'unique'],

            ['cc_name', 'required'],
            ['cc_name', 'string', 'max' => 255],
            ['cc_name', 'unique'],

            ['cc_dep_id', 'required'],
            ['cc_dep_id', 'integer'],
            ['cc_dep_id', 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['cc_dep_id' => 'dep_id']],

            [['cc_system', 'cc_enabled'], 'boolean'],
            ['cc_enabled', 'default', 'value' => true],
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

    public static function getIdByKey(string $key): ?int
    {
        return self::find()->select(['cc_id'])->where(['cc_key' => $key])->limit(1)->scalar();
    }

    public static function getIdListByCategoryKeys(array $categoryKeys): array
    {
        return self::find()->select(['cc_id'])->where(['IN', 'cc_key', $categoryKeys])->column();
    }
}

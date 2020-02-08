<?php

namespace modules\qaTask\src\entities\qaTask;

use common\models\Department;
use common\models\Employee;
use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\entities\qaTaskCategory\QaTaskCategory;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%qa_task}}".
 *
 * @property int $t_id
 * @property string $t_gid
 * @property int $t_object_type_id
 * @property int $t_object_id
 * @property int|null $t_category_id
 * @property int $t_status_id
 * @property int|null $t_rating
 * @property int|null $t_create_type_id
 * @property string|null $t_description
 * @property int|null $t_department_id
 * @property string|null $t_deadline_dt
 * @property int|null $t_assigned_user_id
 * @property int|null $t_created_user_id
 * @property int|null $t_updated_user_id
 * @property string|null $t_created_dt
 * @property string|null $t_updated_dt
 *
 * @property QaTaskCategory $category
 * @property Employee $assignedUser
 * @property Employee $createdUser
 * @property Employee $updatedUser
 * @property Department $department
 */
class QaTask extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%qa_task}}';
    }

    public function rules(): array
    {
        return [
            ['t_gid', 'required'],
            ['t_gid', 'string', 'max' => 32],
            ['t_gid', 'unique'],

            ['t_object_type_id', 'required'],
            ['t_object_type_id', 'integer'],
            ['t_object_type_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['t_object_type_id', 'in', 'range' => array_keys(QaObjectType::getList())],

            ['t_object_id', 'required'],
            ['t_object_id', 'integer'],

            ['t_status_id', 'required'],
            ['t_status_id', 'integer'],
            ['t_status_id', 'in', 'range' => array_keys(QaTaskStatus::getList())],

            ['t_category_id', 'integer'],
            ['t_category_id', 'exist', 'skipOnError' => true, 'targetClass' => QaTaskCategory::class, 'targetAttribute' => ['t_category_id' => 'tc_id']],
            ['t_category_id', function () {
                if ($this->t_object_type_id !== $this->category->tc_object_type_id) {
                    $this->addError('t_category_id', 'Different types Category and Task');
                }
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['t_rating', 'integer', 'min' => 0, 'max' => 9],

            ['t_create_type_id', 'integer'],
            ['t_create_type_id', 'in', 'range' => array_keys(QaTaskCreatedType::getList())],

            ['t_department_id', 'integer'],
            ['t_department_id', 'in', 'range' => array_keys(Department::DEPARTMENT_LIST)],

            ['t_description', 'string'],

            ['t_deadline_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['t_assigned_user_id', 'integer'],
            ['t_assigned_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['t_assigned_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['t_created_dt', 't_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['t_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 't_created_user_id',
                'updatedByAttribute' => 't_updated_user_id',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            't_id' => 'ID',
            't_gid' => 'Gid',
            't_object_type_id' => 'Object Type',
            't_object_id' => 'Object ID',
            't_category_id' => 'Category',
            't_status_id' => 'Status',
            't_rating' => 'Rating',
            't_create_type_id' => 'Create Type',
            't_description' => 'Description',
            't_department_id' => 'Department',
            't_deadline_dt' => 'Deadline Dt',
            't_assigned_user_id' => 'Assigned User',
            'assignedUser' => 'Assigned User',
            't_created_user_id' => 'Created User',
            'createdUser' => 'Created User',
            't_updated_user_id' => 'Updated User',
            'updatedUser' => 'Updated User',
            't_created_dt' => 'Created Dt',
            't_updated_dt' => 'Updated Dt',
            'category.tc_name' => 'Category'
        ];
    }

    public function getAssignedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 't_assigned_user_id']);
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(QaTaskCategory::class, ['tc_id' => 't_category_id']);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 't_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 't_updated_user_id']);
    }

    public function getDepartment(): ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 't_department_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}

<?php

namespace modules\qaTask\src\entities\qaTaskCategory;

use common\models\Employee;
use modules\qaTask\src\entities\QaObjectType;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%qa_task_category}}".
 *
 * @property int $tc_id
 * @property string $tc_key
 * @property int $tc_object_type_id
 * @property string $tc_name
 * @property string|null $tc_description
 * @property int $tc_enabled
 * @property int $tc_default
 * @property int|null $tc_created_user_id
 * @property int|null $tc_updated_user_id
 * @property string $tc_created_dt
 * @property string|null $tc_updated_dt
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class QaTaskCategory extends \yii\db\ActiveRecord
{

    public static function tableName(): string
    {
        return '{{%qa_task_category}}';
    }

    public function rules(): array
    {
        return [
            ['tc_key', 'required'],
            ['tc_key', 'unique'],
            ['tc_key', 'string', 'max' => 30],

            ['tc_object_type_id', 'required'],
            ['tc_object_type_id', 'integer'],
            ['tc_object_type_id', 'in','range' => array_keys(QaObjectType::getList())],

            ['tc_name', 'required'],
            ['tc_name', 'string', 'max' => 30],

            ['tc_description', 'string', 'max' => 255],

            ['tc_enabled', 'required'],
            ['tc_enabled', 'boolean'],

            ['tc_default', 'required'],
            ['tc_default', 'boolean'],

            ['tc_created_user_id', 'integer'],
            ['tc_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tc_created_user_id' => 'id']],

            ['tc_updated_user_id', 'integer'],
            ['tc_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['tc_updated_user_id' => 'id']],

            ['tc_created_dt', 'required'],
            ['tc_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['tc_updated_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'tc_id' => 'ID',
            'tc_key' => 'Key',
            'tc_object_type_id' => 'Object Type',
            'tc_name' => 'Name',
            'tc_description' => 'Description',
            'tc_enabled' => 'Enabled',
            'tc_default' => 'Default',
            'tc_created_user_id' => 'Created User',
            'tc_updated_user_id' => 'Updated User',
            'createdUser' => 'Created User',
            'updatedUser' => 'Updated User',
            'tc_created_dt' => 'Created Dt',
            'tc_updated_dt' => 'Updated Dt',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tc_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tc_updated_user_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}

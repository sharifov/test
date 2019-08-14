<?php

namespace sales\entities\cases;

use common\models\Department;
use common\models\Employee;
use sales\validators\AlphabetValidator;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class CasesCategory
 *
 * @property string $cc_key
 * @property string $cc_name
 * @property int $cc_dep_id
 * @property int $cc_system
 * @property string $cc_created_dt
 * @property string $cc_updated_dt
 * @property int $cc_updated_user_id
 *
 * @property Cases[] $cases
 * @property Department $dep
 * @property Employee $updatedUser
 */
class CasesCategory extends \yii\db\ActiveRecord
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
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['cc_key', 'required'],
            ['cc_key', 'string', 'max' => 50],
            ['cc_key', 'match', 'pattern' => '/^[a-zA-Z0-9_-]+$/', 'message' =>  'Key can only contain alphanumeric characters, underscores and dashes.'],
            ['cc_key', 'unique'],

            ['cc_name', 'required'],
            ['cc_name', 'string', 'max' => 255],
            ['cc_name', 'unique'],

            ['cc_dep_id', 'required'],
            ['cc_dep_id', 'integer'],
            ['cc_dep_id', 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['cc_dep_id' => 'dep_id']],

            ['cc_system', 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cc_key' => 'Key',
            'cc_name' => 'Name',
            'dep.dep_name' => 'Department',
            'cc_dep_id' => 'Department',
            'cc_system' => 'System',
            'cc_created_dt' => 'Created Dt',
            'cc_updated_dt' => 'Updated Dt',
            'cc_updated_user_id' => 'Updated User ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCases(): ActiveQuery
    {
        return $this->hasMany(Cases::className(), ['cs_category' => 'cc_key']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDep(): ActiveQuery
    {
        return $this->hasOne(Department::className(), ['dep_id' => 'cc_dep_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::className(), ['id' => 'cc_updated_user_id']);
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%cases_category}}';
    }

}

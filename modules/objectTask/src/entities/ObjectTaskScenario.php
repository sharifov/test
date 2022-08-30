<?php

namespace modules\objectTask\src\entities;

use common\models\Employee;
use modules\objectTask\src\services\ObjectTaskService;
use src\access\ConditionExpressionService;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\BaseActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "object_task_scenario".
 *
 * @property int $ots_id
 * @property string $ots_key
 * @property string|null $ots_data_json
 * @property int|null $ots_created_user_id
 * @property string|null $ots_created_dt
 * @property string|null $ots_updated_dt
 * @property int|null $ots_updated_user_id
 * @property int|null $ots_enable
 * @property string|null $ots_condition
 * @property string|null $ots_condition_json
 *
 * @property ObjectTask[] $objectTasks
 * @property Employee $otsCreatedUser
 * @property Employee $otsUpdatedUser
 */
class ObjectTaskScenario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'object_task_scenario';
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['ots_created_dt'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['ots_updated_dt']
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['ots_created_user_id'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['ots_updated_user_id']
                ],
                'defaultValue' => null,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['ots_key'], 'required'],
            [['ots_data_json', 'ots_created_dt', 'ots_updated_dt', 'ots_condition_json'], 'safe'],
            [['ots_created_user_id', 'ots_updated_user_id', 'ots_enable'], 'integer'],
            [['ots_key'], 'string', 'max' => 255],
            [['ots_condition'], 'string', 'max' => 3000],
            [['ots_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ots_created_user_id' => 'id']],
            [['ots_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ots_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'ots_id' => 'ID',
            'ots_key' => 'Key',
            'ots_data_json' => 'Parameters',
            'ots_created_user_id' => 'Created User ID',
            'ots_created_dt' => 'Created Dt',
            'ots_updated_dt' => 'Updated Dt',
            'ots_updated_user_id' => 'Updated User ID',
            'ots_enable' => 'Enable',
            'ots_condition' => 'Condition',
            'ots_condition_json' => 'Condition Json',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->ots_data_json && is_string($this->ots_data_json)) {
            $this->ots_data_json = Json::decode($this->ots_data_json);
        }

        if ($this->ots_condition_json) {
            $this->ots_condition = $this->getDecodeCode();
        }

        return true;
    }

    public function getDecodeCode(): string
    {
        $code = '';
        if (is_string($this->ots_condition_json)) {
            $rules = Json::decode($this->ots_condition_json);
        } else {
            $rules = $this->ots_condition_json;
        }

        if (is_array($rules)) {
            $code = ConditionExpressionService::decode($rules);
        }

        return $code;
    }

    /**
     * Gets query for [[ObjectTasks]].
     *
     * @return \yii\db\ActiveQuery|ObjectTaskScopes
     */
    public function getObjectTasks()
    {
        return $this->hasMany(ObjectTask::class, ['ot_ots_id' => 'ots_id']);
    }

    /**
     * Gets query for [[OtsCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|Employee
     */
    public function getOtsCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ots_created_user_id']);
    }

    /**
     * Gets query for [[OtsUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|Employee
     */
    public function getOtsUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ots_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return ObjectTaskScenarioScopes the active query used by this AR class.
     */
    public static function find()
    {
        return new ObjectTaskScenarioScopes(get_called_class());
    }

    public function getStatementAttributesForScenario(): array
    {
        $attributes = [];

        if (!empty($this->ots_key)) {
            $statementObject = ObjectTaskService::getStatementObjectForScenario($this->ots_key);

            if ($statementObject !== null) {
                $attributes = $statementObject->getAttributeList();
            }
        }

        return $attributes;
    }

    public function getScenarioTemplate(): ?array
    {
        $exampleData = null;

        if (!empty($this->ots_key)) {
            $exampleData = ObjectTaskService::getTemplateForScenario($this->ots_key);
        }

        return $exampleData;
    }
}

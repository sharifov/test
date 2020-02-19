<?php

namespace modules\qaTask\src\entities\qaTaskRules;

use common\models\Employee;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * @property int $tr_id
 * @property string $tr_key
 * @property int $tr_type
 * @property string $tr_name
 * @property string|null $tr_description
 * @property string|null $tr_parameters
 * @property int $tr_enabled
 * @property int|null $tr_created_user_id
 * @property int|null $tr_updated_user_id
 * @property string|null $tr_created_dt
 * @property string|null $tr_updated_dt
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class QaTaskRules extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%qa_task_rules}}';
    }

    public function rules(): array
    {
        return [
            ['tr_name', 'required'],
            ['tr_name', 'string', 'max' => 50],

            ['tr_description', 'string', 'max' => 255],

            ['tr_parameters', 'string'],

            ['tr_enabled', 'required'],
            ['tr_enabled', 'boolean'],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['tr_created_dt', 'tr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['tr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'tr_created_user_id',
                'updatedByAttribute' => 'tr_updated_user_id',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'tr_id' => 'ID',
            'tr_key' => 'Key',
            'tr_type' => 'Type',
            'tr_name' => 'Name',
            'tr_description' => 'Description',
            'tr_parameters' => 'Parameters',
            'tr_enabled' => 'Enabled',
            'tr_created_user_id' => 'Created User',
            'createdUser' => 'Created User',
            'tr_updated_user_id' => 'Updated User',
            'updatedUser' => 'Updated User',
            'tr_created_dt' => 'Created Dt',
            'tr_updated_dt' => 'Updated Dt',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tr_created_user_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'tr_updated_user_id']);
    }

    public static function find()
    {
        return new Scopes(static::class);
    }

    public static function getRule(string $key, ?string $subKey = null)
    {
        $value = null;
        if ($rule = self::find()->select(['tr_parameters'])->byKey($key)->asArray()->one()) {
            $parameters = $rule['tr_parameters'];
            try {
                $value = Json::decode($parameters, true);

                if (is_array($value)) {
                    if ($subKey !== null) {
                        if (isset($value[$subKey])) {
                            $value = $value[$subKey];
                        } else {
                            \Yii::error('Key: ' . $key . ' not found subKey: ' . $subKey, 'QaTaskRules:getRule');
                            return null;
                        }
                    }
                } else {
                    if ($subKey !== null) {
                        \Yii::error('Key: ' . $key . ' Value is not array. subKey: ' . $subKey , 'QaTaskRules:getRule');
                        return null;
                    }
                }

            } catch (\Throwable $e) {
                \Yii::error('Key: ' . $key . PHP_EOL . $e, 'QaTaskRules:getRule');
                $value = null;
            }
        }
        return $value;
    }
}

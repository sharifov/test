<?php

namespace src\model\leadBusinessExtraQueueRule\entity;

use common\components\validators\CheckJsonValidator;
use common\components\validators\IsArrayValidator;
use common\models\Employee;
use common\models\Lead;
use src\behaviors\StringToJsonBehavior;
use src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueue;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "lead_business_extra_queue_rules".
 *
 * @property int $lbeqr_id
 * @property int|null $lbeqr_enabled
 * @property string $lbeqr_key
 * @property string $lbeqr_name
 * @property string|null $lbeqr_description
 * @property array|null $lbeqr_params_json
 * @property string|null $lbeqr_updated_dt
 * @property int|null $lbeqr_updated_user_id
 *
 * @property LeadBusinessExtraQueueLog[] $leadBusinessExtraQueueLogs
 * @property LeadBusinessExtraQueue[] $leadBusinessExtraQueues
 * @property Employee $lbeqrUpdatedUser
 */
class LeadBusinessExtraQueueRule extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['lbeqr_description', 'string', 'max' => 500],

            ['lbeqr_enabled', 'integer'],

            ['lbeqr_key', 'required'],
            ['lbeqr_key', 'string', 'max' => 50],
            ['lbeqr_key', 'unique'],
            ['lbeqr_key', 'filter', 'filter' => static function ($value) {
                return Inflector::slug($value, '_');
            }],

            ['lbeqr_name', 'required'],
            ['lbeqr_name', 'string', 'max' => 50],

            ['lbeqr_params_json', CheckJsonValidator::class, 'skipOnEmpty' => true],

            [['lbeqr_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s', 'skipOnEmpty' => true],

            ['lbeqr_updated_user_id', 'integer'],
            ['lbeqr_updated_user_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lbeqr_updated_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lbeqr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lbeqr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lbeqr_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lbeqr_updated_user_id'],
                ],
                'defaultValue' => null
            ],
            'stringToJson' => [
                'class' => StringToJsonBehavior::class,
                'jsonColumn' => 'lppd_params_json',
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getLeadBusinessExtraQueueLogs(): \yii\db\ActiveQuery
    {
        return $this->hasMany(LeadBusinessExtraQueueLog::class, ['lbeql_lbeqr_id' => 'lbeqr_id']);
    }

    public function getLeadBusinessExtraQueues(): \yii\db\ActiveQuery
    {
        return $this->hasMany(LeadBusinessExtraQueue::class, ['lbeq_lbeqr_id' => 'lbeqr_id']);
    }

    public function getLbeqrUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'lbeqr_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'lbeqr_id' => 'ID',
            'lbeqr_enabled' => 'Enabled',
            'lbeqr_key' => 'Key',
            'lbeqr_name' => 'Name',
            'lbeqr_description' => 'Description',
            'lbeqr_params_json' => 'Params',
            'lbeqr_updated_dt' => 'Updated Dt',
            'lbeqr_updated_user_id' => 'Updated User',
        ];
    }

    public static function find(): LeadBusinessExtraQueueRuleScopes
    {
        return new LeadBusinessExtraQueueRuleScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_business_extra_queue_rules';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->lbeqr_enabled;
    }
}

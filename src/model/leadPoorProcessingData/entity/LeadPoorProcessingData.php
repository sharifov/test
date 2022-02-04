<?php

namespace src\model\leadPoorProcessingData\entity;

use common\components\validators\CheckJsonValidator;
use common\components\validators\IsArrayValidator;
use common\models\Employee;
use common\models\Lead;
use src\behaviors\StringToJsonBehavior;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "lead_poor_processing_data".
 *
 * @property int $lppd_id
 * @property int|null $lppd_enabled
 * @property string $lppd_key
 * @property string $lppd_name
 * @property string|null $lppd_description
 * @property int $lppd_minute
 * @property array|null $lppd_params_json
 * @property string|null $lppd_updated_dt
 * @property int|null $lppd_updated_user_id
 *
 * @property LeadPoorProcessingLog[] $leadPoorProcessingLogs
 * @property LeadPoorProcessing[] $leadPoorProcessings
 * @property Lead[] $lppLeads
 * @property Employee $lppdUpdatedUser
 */
class LeadPoorProcessingData extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['lppd_description', 'string', 'max' => 500],

            ['lppd_enabled', 'integer'],

            ['lppd_key', 'required'],
            ['lppd_key', 'string', 'max' => 50],
            ['lppd_key', 'unique'],
            ['lppd_key', 'filter', 'filter' => static function ($value) {
                return Inflector::slug($value, '_');
            }],

            ['lppd_minute', 'integer'],
            ['lppd_minute', 'required'],

            ['lppd_name', 'required'],
            ['lppd_name', 'string', 'max' => 50],

            ['lppd_params_json', CheckJsonValidator::class, 'skipOnEmpty' => true],

            [['lppd_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s', 'skipOnEmpty' => true],

            ['lppd_updated_user_id', 'integer'],
            ['lppd_updated_user_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lppd_updated_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lppd_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lppd_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lppd_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lppd_updated_user_id'],
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

    public function getLeadPoorProcessingLogs(): \yii\db\ActiveQuery
    {
        return $this->hasMany(LeadPoorProcessingLog::class, ['lppl_lppd_id' => 'lppd_id']);
    }

    public function getLeadPoorProcessings(): \yii\db\ActiveQuery
    {
        return $this->hasMany(LeadPoorProcessing::class, ['lpp_lppd_id' => 'lppd_id']);
    }

    public function getLppLeads(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Lead::class, ['id' => 'lpp_lead_id'])->viaTable('lead_poor_processing', ['lpp_lppd_id' => 'lppd_id']);
    }

    public function getLppdUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'lppd_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'lppd_id' => 'ID',
            'lppd_enabled' => 'Enabled',
            'lppd_key' => 'Key',
            'lppd_name' => 'Name',
            'lppd_description' => 'Description',
            'lppd_minute' => 'Minute',
            'lppd_params_json' => 'Params',
            'lppd_updated_dt' => 'Updated Dt',
            'lppd_updated_user_id' => 'Updated User',
        ];
    }

    public static function find(): LeadPoorProcessingDataScopes
    {
        return new LeadPoorProcessingDataScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_poor_processing_data';
    }

    public function isEnabled(): bool
    {
        return (bool) $this->lppd_enabled;
    }
}

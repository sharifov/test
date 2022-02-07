<?php

namespace src\model\leadPoorProcessingLog\entity;

use common\models\Employee;
use common\models\Lead;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_poor_processing_log".
 *
 * @property int $lppl_id
 * @property int $lppl_lead_id
 * @property int $lppl_lppd_id
 * @property int $lppl_status
 * @property int|null $lppl_owner_id
 * @property string|null $lppl_created_dt
 * @property string|null $lppl_updated_dt
 * @property int|null $lppl_updated_user_id
 *
 * @property Lead $lpplLead
 * @property LeadPoorProcessingData $lpplLppd
 * @property Employee|null $owner
 */
class LeadPoorProcessingLog extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['lppl_lead_id', 'required'],
            ['lppl_lead_id', 'integer'],
            ['lppl_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lppl_lead_id' => 'id']],

            ['lppl_lppd_id', 'required'],
            ['lppl_lppd_id', 'integer'],
            ['lppl_lppd_id', 'exist', 'skipOnError' => true, 'targetClass' => LeadPoorProcessingData::class, 'targetAttribute' => ['lppl_lppd_id' => 'lppd_id']],

            ['lppl_owner_id', 'integer'],
            ['lppl_owner_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lppl_owner_id' => 'id']],

            ['lppl_status', 'required'],
            ['lppl_status', 'integer'],
            [['lppl_status'], 'in', 'range' => array_keys(LeadPoorProcessingLogStatus::STATUS_LIST)],

            [['lppl_created_dt', 'lppl_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s', 'skipOnEmpty' => true],

            ['lppl_updated_user_id', 'integer'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lppl_created_dt', 'lppl_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lppl_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getLpplLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'lppl_lead_id']);
    }

    public function getLpplLppd(): \yii\db\ActiveQuery
    {
        return $this->hasOne(LeadPoorProcessingData::class, ['lppd_id' => 'lppl_lppd_id']);
    }

    public function getOwner(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'lppl_owner_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'lppl_id' => 'ID',
            'lppl_lead_id' => 'Lead',
            'lppl_lppd_id' => 'LeadPoorProcessingData',
            'lppl_status' => 'Status',
            'lppl_owner_id' => 'Owner',
            'lppl_created_dt' => 'Created',
            'lppl_updated_dt' => 'Updated',
            'lppl_updated_user_id' => 'Updated User',
        ];
    }

    public static function find(): LeadPoorProcessingLogScopes
    {
        return new LeadPoorProcessingLogScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_poor_processing_log';
    }

    public function getStatusName(): string
    {
        return LeadPoorProcessingLogStatus::STATUS_LIST[$this->lppl_status] ?? '---';
    }

    public static function create(
        int $leadId,
        int $dataId,
        ?int $ownerId,
        int $status
    ): LeadPoorProcessingLog {
        $model = new self();
        $model->lppl_lead_id = $leadId;
        $model->lppl_lppd_id = $dataId;
        $model->lppl_owner_id = $ownerId;
        $model->lppl_status = $status;

        return $model;
    }
}

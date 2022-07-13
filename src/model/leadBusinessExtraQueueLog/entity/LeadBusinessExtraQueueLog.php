<?php

namespace src\model\leadBusinessExtraQueueLog\entity;

use common\models\Employee;
use common\models\Lead;
use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_business_extra_queue_log".
 *
 * @property int $lbeql_id
 * @property int $lbeql_lead_id
 * @property int $lbeql_lbeqr_id
 * @property int $lbeql_status
 * @property int|null $lbeql_owner_id
 * @property string|null $lbeql_created_dt
 * @property string|null $lbeql_updated_dt
 *
 * @property Lead $lbeqlLead
 * @property LeadBusinessExtraQueueRule $lbeqlLbeqr
 * @property Employee|null $owner
 */
class LeadBusinessExtraQueueLog extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['lbeql_lead_id', 'required'],
            ['lbeql_lead_id', 'integer'],
            ['lbeql_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lbeql_lead_id' => 'id']],

            ['lbeql_lbeqr_id', 'required'],
            ['lbeql_lbeqr_id', 'integer'],
            ['lbeql_lbeqr_id', 'exist', 'skipOnError' => true, 'targetClass' => LeadBusinessExtraQueueRule::class, 'targetAttribute' => ['lbeql_lbeqr_id' => 'lbeql_id']],

            ['lbeql_owner_id', 'integer'],
            ['lbeql_owner_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lbeql_owner_id' => 'id']],

            ['lbeql_status', 'required'],
            ['lbeql_status', 'integer'],
           // [['lbeql_status'], 'in', 'range' => array_keys(LeadPoorProcessingLogStatus::STATUS_LIST)],

            [['lbeql_created_dt', 'lbeql_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s', 'skipOnEmpty' => true],

        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lbeql_created_dt', 'lbeql_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lbeql_updated_dt'],
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

    public function getLbeqlLbeqr(): \yii\db\ActiveQuery
    {
        return $this->hasOne(LeadBusinessExtraQueueRule::class, ['lbeqr_id' => 'lbeql_lbeqr_id']);
    }

    public function getOwner(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'lbeql_owner_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'lbeql_id' => 'ID',
            'lbeql_lead_id' => 'Lead',
            'lbeql_lbeqr_id' => 'LeadBusinessExtraQueueRule',
            'lbeql_status' => 'Status',
            'lbeql_owner_id' => 'Owner',
            'lbeql_created_dt' => 'Created',
            'lbeql_updated_dt' => 'Updated',
            'lbeql_updated_user_id' => 'Updated User',
            'lbeql_description' => 'Description',
        ];
    }

    public static function find(): LeadBusinessExtraQueueLogScopes
    {
        return new LeadBusinessExtraQueueLogScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_business_extra_queue_log';
    }

//    public function getStatusName(): string
//    {
//        return LeadPoorProcessingLogStatus::STATUS_LIST[$this->lppl_status] ?? '---';
//    }

    public static function create(
        int $leadId,
        int $dataId,
        ?int $ownerId,
        int $status
    ): LeadBusinessExtraQueueLog {
        $model = new self();
        $model->lbeql_lead_id = $leadId;
        $model->lbeql_lbeqr_id = $dataId;
        $model->lbeql_owner_id = $ownerId;
        $model->lbeql_status = $status;
        return $model;
    }
}

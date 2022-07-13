<?php

namespace src\model\leadBusinessExtraQueue\entity;

use common\models\Lead;
use src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule;
use src\traits\FieldsTrait;
use Yii;

/**
 * This is the model class for table "lead_business_extra_queue".
 *
 * @property int $lbeq_lead_id
 * @property int $lbeq_lbeqr_id
 * @property string|null $lbeq_expiration_dt
 * @property string|null $lbeq_created_dt
 *
 * @property Lead $lbeqLead
 * @property LeadBusinessExtraQueueRule $lbeqLbeqr
 */
class LeadBusinessExtraQueue extends \yii\db\ActiveRecord
{
    use FieldsTrait;

    public function rules(): array
    {
        return [
            [['lbeq_lead_id', 'lbeq_lbeqr_id'], 'unique', 'targetAttribute' => ['lbeq_lead_id', 'lbeq_lbeqr_id']],

            ['lbeq_lead_id', 'required'],
            ['lbeq_lead_id', 'integer'],
            ['lbeq_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lbeq_lead_id' => 'id']],

            ['lbeq_lbeqr_id', 'required'],
            ['lbeq_lbeqr_id', 'integer'],
            ['lbeq_lbeqr_id', 'exist', 'skipOnError' => true, 'targetClass' => LeadBusinessExtraQueueRule::class, 'targetAttribute' => ['lbeq_lbeqr_id' => 'lbeqr_id']],

            ['lbeq_expiration_dt', 'required'],
            [['lbeq_expiration_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function getLbeqLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'lpp_lead_id']);
    }

    public function getLbeqLbeqr(): \yii\db\ActiveQuery
    {
        return $this->hasOne(LeadBusinessExtraQueueRule::class, ['lbeqr_id' => 'lbeq_lbeqr_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'lbeq_lead_id' => 'Lead',
            'lbeq_lbeqr_id' => 'Lead Business Extra Queue Rule',
            'lbeq_created_dt' => 'Created Dt',
            'lbeq_expiration_dt' => 'Expiration Dt',
        ];
    }

    public static function find(): LeadBusinessExtraQueueScopes
    {
        return new LeadBusinessExtraQueueScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_business_extra_queue';
    }

    public static function create(
        int $leadId,
        int $dataId,
        string $expirationDt
    ): LeadBusinessExtraQueue {
        $model = new self();
        $model->lbeq_lead_id = $leadId;
        $model->lbeq_lppd_id = $dataId;
        $model->lbeq_expiration_dt = $expirationDt;
        return $model;
    }
}

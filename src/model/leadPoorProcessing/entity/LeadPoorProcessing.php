<?php

namespace src\model\leadPoorProcessing\entity;

use common\models\Lead;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\traits\FieldsTrait;
use Yii;

/**
 * This is the model class for table "lead_poor_processing".
 *
 * @property int $lpp_lead_id
 * @property int $lpp_lppd_id
 * @property string|null $lpp_expiration_dt
 *
 * @property Lead $lppLead
 * @property LeadPoorProcessingData $lppLppd
 */
class LeadPoorProcessing extends \yii\db\ActiveRecord
{
    use FieldsTrait;

    public function rules(): array
    {
        return [
            [['lpp_lead_id', 'lpp_lppd_id'], 'unique', 'targetAttribute' => ['lpp_lead_id', 'lpp_lppd_id']],

            ['lpp_lead_id', 'required'],
            ['lpp_lead_id', 'integer'],
            ['lpp_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lpp_lead_id' => 'id']],

            ['lpp_lppd_id', 'required'],
            ['lpp_lppd_id', 'integer'],
            ['lpp_lppd_id', 'exist', 'skipOnError' => true, 'targetClass' => LeadPoorProcessingData::class, 'targetAttribute' => ['lpp_lppd_id' => 'lppd_id']],

            ['lpp_expiration_dt', 'required'],
            [['lpp_expiration_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function getLppLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'lpp_lead_id']);
    }

    public function getLppLppd(): \yii\db\ActiveQuery
    {
        return $this->hasOne(LeadPoorProcessingData::class, ['lppd_id' => 'lpp_lppd_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'lpp_lead_id' => 'Lead',
            'lpp_lppd_id' => 'LeadPoorProcessingData',
            'lpp_expiration_dt' => 'Expiration Dt',
        ];
    }

    public static function find(): LeadPoorProcessingScopes
    {
        return new LeadPoorProcessingScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_poor_processing';
    }

    public static function create(
        int $leadId,
        int $dataId,
        string $expirationDt
    ): LeadPoorProcessing {
        $model = new self();
        $model->lpp_lead_id = $leadId;
        $model->lpp_lppd_id = $dataId;
        $model->lpp_expiration_dt = $expirationDt;
        return $model;
    }
}

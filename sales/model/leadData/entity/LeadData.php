<?php

namespace sales\model\leadData\entity;

use common\models\Lead;
use sales\model\leadData\services\LeadDataDictionary;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_data".
 *
 * @property int $ld_id
 * @property int|null $ld_lead_id
 * @property string $ld_field_key
 * @property string|null $ld_field_value
 * @property string|null $ld_created_dt
 *
 * @property Lead $ldLead
 */
class LeadData extends \yii\db\ActiveRecord
{
    public static function tableName(): string
    {
        return 'lead_data';
    }

    public function rules(): array
    {
        return [
            [['ld_lead_id', 'ld_field_key'], 'required'],

            [['ld_lead_id', 'ld_field_key'], 'unique', 'targetAttribute' => ['ld_lead_id', 'ld_field_key']],

            ['ld_field_key', 'string', 'max' => 50],
            ['ld_field_key', 'in', 'range' => array_keys(LeadDataDictionary::KEY_LIST)],

            ['ld_field_value', 'string', 'max' => 500],

            ['ld_lead_id', 'integer'],
            ['ld_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['ld_lead_id' => 'id']],

            ['ld_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ld_created_dt', 'ld_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ld_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getLdLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'ld_lead_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ld_id' => 'ID',
            'ld_lead_id' => 'Lead ID',
            'ld_field_key' => 'Field Key',
            'ld_field_value' => 'Field Value',
            'ld_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): LeadDataScopes
    {
        return new LeadDataScopes(static::class);
    }

    public static function create(
        int $leadId,
        ?string $fieldKey,
        ?string $fieldValue
    ): LeadData {
        $model = new self();
        $model->ld_lead_id = $leadId;
        $model->ld_field_key = $fieldKey;
        $model->ld_field_value = $fieldValue;

        return $model;
    }
}

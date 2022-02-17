<?php

namespace src\model\leadUserData\entity;

use common\models\Employee;
use common\models\Lead;
use Yii;

/**
 * This is the model class for table "lead_user_data".
 *
 * @property int $lud_id
 * @property int $lud_type_id
 * @property int $lud_lead_id
 * @property int $lud_user_id
 * @property string $lud_created_dt
 *
 * @property Lead $ludLead
 * @property Employee $ludUser
 */
class LeadUserData extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['lud_lead_id', 'required'],
            ['lud_lead_id', 'integer'],
            ['lud_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lud_lead_id' => 'id']],

            ['lud_type_id', 'required'],
            ['lud_type_id', 'integer'],
            ['lud_type_id', 'in', 'range' => array_keys(LeadUserDataDictionary::TYPE_LIST)],

            ['lud_user_id', 'required'],
            ['lud_user_id', 'integer'],
            ['lud_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lud_user_id' => 'id']],

            ['lud_created_dt', 'required'],
            ['lud_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function getLudLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'lud_lead_id']);
    }

    public function getLudUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'lud_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'lud_id' => 'ID',
            'lud_type_id' => 'Type',
            'lud_lead_id' => 'Lead',
            'lud_user_id' => 'User',
            'lud_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): LeadUserDataScope
    {
        return new LeadUserDataScope(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_user_data';
    }

    public function getTypeName(): ?string
    {
        return LeadUserDataDictionary::TYPE_LIST[$this->lud_type_id] ?? null;
    }

    public static function create(
        int $typeId,
        int $leadId,
        int $userId,
        \DateTimeImmutable $createdDt
    ): LeadUserData {
        $model = new self();
        $model->lud_type_id = $typeId;
        $model->lud_lead_id = $leadId;
        $model->lud_user_id = $userId;
        $model->lud_created_dt = $createdDt->format('Y-m-d H:i:s');

        return $model;
    }
}

<?php

namespace sales\model\leadRedial\entity;

use common\models\Employee;
use common\models\LeadQcall;

/**
 * This is the model class for table "{{%call_redial_user_access}}".
 *
 * @property int $crua_lead_id
 * @property int $crua_user_id
 * @property string $crua_created_dt
 * @property string|null $crua_updated_dt
 *
 * @property LeadQcall $qcall
 */
class CallRedialUserAccess extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['crua_created_dt', 'required'],
            ['crua_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['crua_lead_id', 'required'],
            ['crua_lead_id', 'integer'],
            ['crua_lead_id', 'unique'],
            ['crua_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => LeadQcall::class, 'targetAttribute' => ['crua_lead_id' => 'lqc_lead_id']],

            ['crua_updated_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['crua_user_id', 'required'],
            ['crua_user_id', 'integer'],
            ['crua_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['crua_user_id' => 'id']],
        ];
    }

    public function getQcall(): \yii\db\ActiveQuery
    {
        return $this->hasOne(LeadQcall::class, ['lqc_lead_id' => 'crua_lead_id']);
    }

    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'crua_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'crua_lead_id' => 'Lead ID',
            'crua_user_id' => 'User ID',
            'crua_created_dt' => 'Created Dt',
            'crua_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%call_redial_user_access}}';
    }
}

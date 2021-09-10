<?php

namespace sales\model\leadUserConversion\entity;

use common\models\Employee;
use common\models\Lead;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_user_conversion".
 *
 * @property int $luc_lead_id
 * @property int $luc_user_id
 * @property string|null $luc_description
 * @property string|null $luc_created_dt
 * @property int|null $luc_created_user_id
 *
 * @property Lead $lucLead
 * @property Employee $lucUser
 * @property Employee|null $createdUser
 */
class LeadUserConversion extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['luc_lead_id', 'luc_user_id'], 'unique', 'targetAttribute' => ['luc_lead_id', 'luc_user_id']],

            ['luc_description', 'string', 'max' => 100],

            ['luc_lead_id', 'required'],
            ['luc_lead_id', 'integer'],
            ['luc_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['luc_lead_id' => 'id']],

            ['luc_user_id', 'required'],
            ['luc_user_id', 'integer'],
            ['luc_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['luc_user_id' => 'id']],

            ['luc_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['luc_created_user_id', 'integer'],
            ['luc_created_user_id', 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['luc_created_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['luc_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getLucLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'luc_lead_id']);
    }

    public function getLucUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'luc_user_id']);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'luc_created_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'luc_lead_id' => 'Lead',
            'luc_user_id' => 'User',
            'luc_description' => 'Description',
            'luc_created_dt' => 'Created Dt',
            'luc_created_user_id' => 'Created user',
        ];
    }

    public static function find(): LeadUserConversionScopes
    {
        return new LeadUserConversionScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_user_conversion';
    }

    public static function create(
        int $leadId,
        int $userId,
        ?string $description = null,
        ?int $createdUserId = null
    ): LeadUserConversion {
        $model = new self();
        $model->luc_lead_id = $leadId;
        $model->luc_user_id = $userId;
        $model->luc_description = $description;
        $model->luc_created_user_id = $createdUserId;
        return $model;
    }
}

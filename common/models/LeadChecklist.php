<?php

namespace common\models;

use common\models\query\LeadChecklistQuery;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lead_checklist".
 *
 * @property int $lc_type_id
 * @property int $lc_lead_id
 * @property int $lc_user_id
 * @property string $lc_notes
 * @property string $lc_created_dt
 *
 * @property LeadChecklistType $lcType
 * @property Lead $lcLead
 * @property Employee $lcUser
 */
class LeadChecklist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_checklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lc_type_id', 'lc_lead_id', 'lc_user_id'], 'required'],
            [['lc_type_id', 'lc_lead_id', 'lc_user_id'], 'integer'],
            [['lc_created_dt'], 'safe'],
            [['lc_notes'], 'string', 'max' => 500],
            [['lc_type_id', 'lc_lead_id', 'lc_user_id'], 'unique', 'targetAttribute' => ['lc_type_id', 'lc_lead_id', 'lc_user_id']],
            [['lc_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadChecklistType::class, 'targetAttribute' => ['lc_type_id' => 'lct_id']],
            [['lc_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lc_lead_id' => 'id']],
            [['lc_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lc_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lc_type_id' => 'Type',
            'lc_lead_id' => 'Lead',
            'lc_user_id' => 'User',
            'lc_notes' => 'Notes',
            'lc_created_dt' => 'Created Dt',
        ];
    }


    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lc_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lc_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'lc_user_id',
                'updatedByAttribute' => 'lc_user_id',
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLcType()
    {
        return $this->hasOne(LeadChecklistType::class, ['lct_id' => 'lc_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLcLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'lc_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLcUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'lc_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return LeadChecklistQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LeadChecklistQuery(static::class);
    }
}

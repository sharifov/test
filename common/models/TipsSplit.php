<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "tips_split".
 *
 * @property int $ts_id
 * @property int $ts_lead_id
 * @property int $ts_user_id
 * @property int $ts_percent
 * @property int $ts_amount
 * @property string $ts_updated_dt
 * @property int $ts_updated_user_id
 *
 * @property Employee $tsUpdatedUser
 * @property Lead $tsLead
 * @property Employee $tsUser
 */
class TipsSplit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tips_split';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_lead_id', 'ts_user_id'], 'required'],
            [['ts_lead_id', 'ts_user_id', 'ts_amount', 'ts_updated_user_id'], 'integer'],
            [['ts_percent'], 'integer', 'min' => 1, 'skipOnEmpty' => false],
            [['ts_updated_dt'], 'safe'],
            [['ts_user_id', 'ts_lead_id'], 'unique', 'targetAttribute' => ['ts_user_id', 'ts_lead_id']],
            [['ts_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ts_updated_user_id' => 'id']],
            [['ts_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['ts_lead_id' => 'id']],
            [['ts_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ts_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ts_id' => 'ID',
            'ts_lead_id' => 'Lead ID',
            'ts_user_id' => 'User ID',
            'ts_percent' => 'Percent',
            'ts_amount' => 'Amount',
            'ts_updated_dt' => 'Updated Dt',
            'ts_updated_user_id' => 'Updated User ID',
        ];
    }


    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ts_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ts_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTsUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ts_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTsLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'ts_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTsUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ts_user_id']);
    }


    public function countTips($total)
    {
        if (!empty($this->ts_percent)) {
            return $total * $this->ts_percent / 100;
        }

        return 0;
    }

    /**
     * @param int $leadId
     * @param int $userId
     * @param int $percent
     * @return TipsSplit
     */
    public static function create(int $leadId, int $userId, int $percent): TipsSplit
    {
        $self = new self();
        $self->ts_lead_id = $leadId;
        $self->ts_user_id = $userId;
        $self->ts_percent = $percent;
        return $self;
    }
}

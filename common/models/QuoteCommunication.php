<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "email_quote".
 *
 * @property int $qc_id
 * @property int $qc_communication_type
 * @property int $qc_communication_id
 * @property int $qc_quote_id
 * @property string|null $qc_created_dt
 * @property int|null $qc_created_by
 *
 * @property Employee $createdBy
 * @property Quote $quote
 */
class QuoteCommunication extends ActiveRecord
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['qc_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'qc_created_by',
                'updatedByAttribute' => false
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quote_communication';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qc_communication_id', 'qc_communication_type', 'qc_quote_id'], 'required'],
            [['qc_communication_id', 'qc_communication_type', 'qc_quote_id', 'qc_created_by'], 'integer'],
            [['qc_created_dt'], 'safe'],
            [['qc_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['qc_created_by' => 'id']],
            [['qc_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quote::class, 'targetAttribute' => ['qc_quote_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'qc_id' => 'ID',
            'qc_communication_type' => 'Communication Type',
            'qc_communication_id' => 'Communication ID',
            'qc_quote_id' => 'Quote ID',
            'qc_created_dt' => 'Created Dt',
            'qc_created_by' => 'Created By'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(Employee::class, ['id' => 'qc_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuote()
    {
        return $this->hasOne(Quote::class, ['id' => 'qc_quote_id']);
    }
}

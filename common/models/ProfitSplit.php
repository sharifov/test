<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profit_split".
 *
 * @property int $ps_id
 * @property int $ps_lead_id
 * @property int $ps_user_id
 * @property int $ps_percent
 * @property int $ps_amount
 * @property string $ps_updated_dt
 * @property int $ps_updated_user_id
 *
 * @property Lead $psLead
 * @property Employee $psUpdatedUser
 * @property Employee $psUser
 */
class ProfitSplit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profit_split';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ps_lead_id', 'ps_user_id'], 'required'],
            ['ps_percent', 'required', 'when' => function($model) {
                return empty($model->ps_amount);
            }],
            ['ps_amount', 'required', 'when' => function($model) {
                return empty($model->ps_percent);
            }],
            ['ps_percent', 'integer', 'max' => 100 , 'min' => 0],
            [['ps_lead_id', 'ps_user_id', 'ps_percent', 'ps_amount', 'ps_updated_user_id'], 'integer'],
            [['ps_updated_dt','ps_percent','ps_amount'], 'safe'],
            [['ps_user_id', 'ps_lead_id'], 'unique', 'targetAttribute' => ['ps_user_id', 'ps_lead_id']],
            [['ps_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['ps_lead_id' => 'id']],
            [['ps_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ps_updated_user_id' => 'id']],
            [['ps_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ps_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ps_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ps_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

/*
    public function beforeSave($insert): bool
    {
        if ($this->isNewRecord) {
            if (!Yii::$app->user->isGuest && Yii::$app->user->identityClass != 'webapi\models\ApiUser' && empty($this->ps_updated_user_id)) {
                $this->ps_updated_user_id = Yii::$app->user->id;
            }
        }
    }

    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

    } */

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ps_id' => 'ID',
            'ps_lead_id' => 'Lead ID',
            'ps_user_id' => 'Agent',
            'ps_percent' => 'Percent',
            'ps_amount' => 'Amount',
            'ps_updated_dt' => 'Updated',
            'ps_updated_user_id' => 'Updated By User',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPsLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'ps_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPsUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ps_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPsUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ps_user_id']);
    }

    public function countProfit($total)
    {
        if(!empty($this->ps_percent)){
            return $total * $this->ps_percent / 100;
        }

        return 0;
    }
}

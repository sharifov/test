<?php

namespace common\models;

use common\models\query\ProfitBonusQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profit_bonus".
 *
 * @property int $pb_id
 * @property int $pb_user_id
 * @property int $pb_min_profit
 * @property int $pb_bonus
 * @property string $pb_updated_dt
 * @property int $pb_updated_user_id
 *
 * @property Employee $pbUpdatedUser
 * @property Employee $pbUser
 */
class ProfitBonus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profit_bonus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pb_user_id', 'pb_min_profit', 'pb_bonus'], 'required'],
            [['pb_user_id', 'pb_min_profit', 'pb_bonus', 'pb_updated_user_id'], 'integer'],
            [['pb_updated_dt'], 'safe'],
            [['pb_user_id', 'pb_min_profit'], 'unique', 'targetAttribute' => ['pb_user_id', 'pb_min_profit']],
            [['pb_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pb_updated_user_id' => 'id']],
            [['pb_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pb_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pb_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pb_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pb_id' => 'ID',
            'pb_user_id' => 'User ID',
            'pb_min_profit' => 'Min Profit',
            'pb_bonus' => 'Bonus',
            'pb_updated_dt' => 'Updated',
            'pb_updated_user_id' => 'Updated by user',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPbUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pb_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPbUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pb_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return ProfitBonusQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProfitBonusQuery(get_called_class());
    }
}

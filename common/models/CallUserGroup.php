<?php

namespace common\models;

use common\models\query\CallUserGroupQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "call_user_group".
 *
 * @property int $cug_c_id
 * @property int $cug_ug_id
 * @property string $cug_created_dt
 *
 * @property Call $cugC
 * @property UserGroup $cugUg
 */
class CallUserGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_user_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cug_c_id', 'cug_ug_id'], 'required'],
            [['cug_c_id', 'cug_ug_id'], 'integer'],
            [['cug_created_dt'], 'safe'],
            [['cug_c_id', 'cug_ug_id'], 'unique', 'targetAttribute' => ['cug_c_id', 'cug_ug_id']],
            [['cug_c_id'], 'exist', 'skipOnError' => true, 'targetClass' => Call::class, 'targetAttribute' => ['cug_c_id' => 'c_id']],
            [['cug_ug_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserGroup::class, 'targetAttribute' => ['cug_ug_id' => 'ug_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cug_c_id' => 'Call ID',
            'cug_ug_id' => 'User Group ID',
            'cug_created_dt' => 'Created Dt',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cug_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cug_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCugC()
    {
        return $this->hasOne(Call::class, ['c_id' => 'cug_c_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCugUg()
    {
        return $this->hasOne(UserGroup::class, ['ug_id' => 'cug_ug_id']);
    }

    /**
     * {@inheritdoc}
     * @return CallUserGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CallUserGroupQuery(static::class);
    }
}

<?php

namespace common\models;

use common\models\query\UserGroupAssignQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_group_assign".
 *
 * @property int $ugs_user_id
 * @property int $ugs_group_id
 * @property string $ugs_updated_dt
 *
 * @property UserGroup $ugsGroup
 * @property Employee $ugsUser
 */
class UserGroupAssign extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_group_assign';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ugs_user_id', 'ugs_group_id'], 'required'],
            [['ugs_user_id', 'ugs_group_id'], 'integer'],
            [['ugs_updated_dt'], 'safe'],
            [['ugs_user_id', 'ugs_group_id'], 'unique', 'targetAttribute' => ['ugs_user_id', 'ugs_group_id']],
            [['ugs_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserGroup::class, 'targetAttribute' => ['ugs_group_id' => 'ug_id']],
            [['ugs_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ugs_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ugs_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ugs_updated_dt'],
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
            'ugs_user_id' => 'User',
            'ugs_group_id' => 'Group',
            'ugs_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUgsGroup()
    {
        return $this->hasOne(UserGroup::class, ['ug_id' => 'ugs_group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUgsUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ugs_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserGroupAssignQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserGroupAssignQuery(static::class);
    }
}

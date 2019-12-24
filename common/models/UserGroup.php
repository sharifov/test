<?php

namespace common\models;

use common\models\query\UserGroupQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_group".
 *
 * @property int $ug_id
 * @property string $ug_key
 * @property string $ug_name
 * @property string $ug_description
 * @property int $ug_disable
 * @property string $ug_updated_dt
 * @property int $ug_processing_fee
 * @property boolean $ug_on_leaderboard
 * @property int $ug_user_group_set_id
 *
 * @property UserGroupAssign[] $userGroupAssigns
 * @property Employee[] $ugsUsers
 * @property UserGroupSet $userGroupSet
 */
class UserGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ug_key', 'ug_name'], 'required'],
            [['ug_disable', 'ug_processing_fee'], 'integer'],
            [['ug_updated_dt'], 'safe'],
            [['ug_key', 'ug_name'], 'string', 'max' => 100],
            [['ug_description'], 'string', 'max' => 255],
            [['ug_key'], 'unique'],
            [['ug_on_leaderboard'], 'boolean'],
            ['ug_user_group_set_id', 'exist', 'targetClass' => UserGroupSet::class, 'targetAttribute' => ['ug_user_group_set_id' => 'ugs_id']]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ug_id' => 'ID',
            'ug_key' => 'Key',
            'ug_name' => 'Name',
            'ug_description' => 'Description',
            'ug_disable' => 'Disable',
            'ug_updated_dt' => 'Updated Dt',
            'ug_processing_fee' => 'Processing Fee',
            'ug_on_leaderboard' => 'Show on Leaderboard',
            'ug_user_group_set_id' => 'User Group Set',
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ug_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ug_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroupSet()
    {
        return $this->hasOne(UserGroupSet::class, ['ugs_id' => 'ug_user_group_set_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserGroupAssigns()
    {
        return $this->hasMany(UserGroupAssign::class, ['ugs_group_id' => 'ug_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUgsUsers()
    {
        return $this->hasMany(Employee::class, ['id' => 'ugs_user_id'])->viaTable('user_group_assign', ['ugs_group_id' => 'ug_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserGroupQuery(get_called_class());
    }

    /**
     * @return array
     */
    public static function getList() : array
    {
        $data = self::find()->orderBy(['ug_name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data,'ug_id', 'ug_name');
    }
}

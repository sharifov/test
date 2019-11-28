<?php

namespace common\models;

use common\models\query\DepartmentPhoneProjectUserGroupQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "department_phone_project_user_group".
 *
 * @property int $dug_dpp_id
 * @property int $dug_ug_id
 * @property string $dug_created_dt
 *
 * @property DepartmentPhoneProject $dugDpp
 * @property UserGroup $dugUg
 */
class DepartmentPhoneProjectUserGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department_phone_project_user_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dug_dpp_id', 'dug_ug_id'], 'required'],
            [['dug_dpp_id', 'dug_ug_id'], 'integer'],
            [['dug_created_dt'], 'safe'],
            [['dug_dpp_id', 'dug_ug_id'], 'unique', 'targetAttribute' => ['dug_dpp_id', 'dug_ug_id']],
            [['dug_dpp_id'], 'exist', 'skipOnError' => true, 'targetClass' => DepartmentPhoneProject::class, 'targetAttribute' => ['dug_dpp_id' => 'dpp_id']],
            [['dug_ug_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserGroup::class, 'targetAttribute' => ['dug_ug_id' => 'ug_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dug_dpp_id' => 'Department phone project ID',
            'dug_ug_id' => 'User Group ID',
            'dug_created_dt' => 'Created Dt',
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['dug_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['dug_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDugDpp()
    {
        return $this->hasOne(DepartmentPhoneProject::class, ['dpp_id' => 'dug_dpp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDugUg()
    {
        return $this->hasOne(UserGroup::class, ['ug_id' => 'dug_ug_id']);
    }

    /**
     * {@inheritdoc}
     * @return DepartmentPhoneProjectUserGroupQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DepartmentPhoneProjectUserGroupQuery(get_called_class());
    }
}

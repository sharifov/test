<?php

namespace common\models;

use common\models\query\UserConnectionQuery;
use sales\entities\cases\Cases;
use Yii;

/**
 * This is the model class for table "user_connection".
 *
 * @property int $uc_id
 * @property int $uc_connection_id
 * @property int $uc_user_id
 * @property int $uc_lead_id
 * @property string $uc_user_agent
 * @property string $uc_controller_id
 * @property string $uc_action_id
 * @property string $uc_page_url
 * @property string $uc_ip
 * @property string $uc_created_dt
 * @property int $uc_case_id
 * @property string $uc_connection
 *
 * @property Cases $ucCase
 * @property Lead $ucLead
 * @property Employee $ucUser
 */
class UserConnection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_connection';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uc_connection_id'], 'required'],
            [['uc_connection_id', 'uc_user_id', 'uc_lead_id', 'uc_case_id'], 'integer'],
            [['uc_created_dt'], 'safe'],
            [['uc_user_agent'], 'string', 'max' => 255],
            [['uc_controller_id', 'uc_action_id'], 'string', 'max' => 50],
            [['uc_page_url'], 'string', 'max' => 500],
            [['uc_ip'], 'string', 'max' => 40],
            [['uc_connection'], 'string'],
            [['uc_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['uc_case_id' => 'cs_id']],
            [['uc_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['uc_lead_id' => 'id']],
            [['uc_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uc_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'uc_id' => 'ID',
            'uc_connection_id' => 'Connection ID',
            'uc_user_id' => 'User ID',
            'uc_lead_id' => 'Lead ID',
            'uc_user_agent' => 'User Agent',
            'uc_controller_id' => 'Controller',
            'uc_action_id' => 'Action',
            'uc_page_url' => 'Page Url',
            'uc_ip' => 'IP',
            'uc_created_dt' => 'Created Dt',
            'uc_case_id' => 'Case ID',
            'uc_connection' => 'Connection Object'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUcCase()
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'uc_case_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUcLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'uc_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUcUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'uc_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserConnectionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserConnectionQuery(static::class);
    }
}

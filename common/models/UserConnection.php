<?php

namespace common\models;

use common\models\query\UserConnectionQuery;
use sales\dispatchers\NativeEventDispatcher;
use sales\entities\cases\Cases;
use sales\model\user\entity\userConnection\events\UserConnectionEvents;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
 * @property string $uc_connection_uid
 *
 * @property Cases $ucCase
 * @property Lead $ucLead
 * @property Employee $ucUser
 */
class UserConnection extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_connection';
    }

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['uc_connection_id'], 'required'],
            [['uc_connection_id', 'uc_user_id', 'uc_lead_id', 'uc_case_id'], 'integer'],
            [['uc_connection_uid'], 'string', 'max' => 30],
            [['uc_connection_uid'], 'unique'],
            [['uc_user_agent'], 'string', 'max' => 255],
            [['uc_controller_id', 'uc_action_id'], 'string', 'max' => 50],
            [['uc_page_url'], 'string', 'max' => 500],
            [['uc_ip'], 'string', 'max' => 40],
            [['uc_created_dt'], 'safe'],
            [['uc_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['uc_case_id' => 'cs_id']],
            [['uc_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['uc_lead_id' => 'id']],
            [['uc_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['uc_user_id' => 'id']],
        ];
    }

    /**
     * @return array|string[]
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
            'uc_connection_uid' => 'Connection UID'
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['uc_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUcCase(): ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'uc_case_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUcLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'uc_lead_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUcUser(): ActiveQuery
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

    /**
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            NativeEventDispatcher::recordEvent(UserConnectionEvents::class, UserConnectionEvents::INSERT, [UserConnectionEvents::class, 'insertUserOnline'], $this);
            NativeEventDispatcher::trigger(UserConnectionEvents::class, UserConnectionEvents::INSERT);
        }
    }


    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        NativeEventDispatcher::recordEvent(UserConnectionEvents::class, UserConnectionEvents::DELETE, [UserConnectionEvents::class, 'deleteUserOnline'], $this);
        return true;
    }

    public function afterDelete(): void
    {
        parent::afterDelete();
        NativeEventDispatcher::trigger(UserConnectionEvents::class, UserConnectionEvents::DELETE);
    }
}

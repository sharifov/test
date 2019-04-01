<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $up_user_id
 * @property int $up_call_type_id
 * @property string $up_sip
 * @property string $up_telegram
 * @property int $up_telegram_enable
 * @property string $up_updated_dt
 * @property boolean $up_auto_redial
 *
 * @property Employee $upUser
 */
class UserProfile extends \yii\db\ActiveRecord
{

    public const CALL_TYPE_OFF = 0;
    public const CALL_TYPE_SIP = 1;
    public const CALL_TYPE_WEB = 2;

    public const CALL_TYPE_LIST = [
        self::CALL_TYPE_OFF => 'Off',
        self::CALL_TYPE_SIP => 'SIP',
        self::CALL_TYPE_WEB => 'Web',
    ];


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['up_user_id'], 'required'],
            [['up_user_id', 'up_call_type_id'], 'integer'],
            [['up_user_id'], 'unique'],
            [['up_telegram_enable', 'up_auto_redial'], 'boolean'],
            [['up_updated_dt'], 'safe'],
            [['up_sip'], 'string', 'max' => 255],
            [['up_telegram'], 'string', 'max' => 20],
            [['up_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['up_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'up_user_id' => 'User ID',
            'up_call_type_id' => 'Call Type',
            'up_sip' => 'Sip',
            'up_telegram' => 'Telegram ID',
            'up_telegram_enable' => 'Telegram Enable',
            'up_updated_dt' => 'Updated Dt',
            'up_auto_redial'    => 'Auto redial'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'up_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserProfileQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserProfileQuery(get_called_class());
    }
}

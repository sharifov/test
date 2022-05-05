<?php

namespace common\models;

use common\models\query\UserProfileQuery;
use src\access\CallAccess;
use SebastianBergmann\Comparator\DateTimeComparatorTest;
use Yii;
use yii\caching\TagDependency;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $up_user_id
 * @property int $up_call_type_id
 * @property string $up_telegram
 * @property int $up_telegram_enable
 * @property string $up_updated_dt
 * @property boolean $up_auto_redial
 * @property boolean $up_kpi_enable
 * @property boolean $up_2fa_enable
 * @property string|null $up_2fa_secret
 * @property mixed|null $up_2fa_timestamp
 * @property int $up_skill
 * @property boolean $up_show_in_contact_list
 *
 * @property string $up_join_date
 *
 * @property string $up_rc_auth_token
 * @property string $up_rc_user_id
 * @property string $up_rc_user_password
 * @property string $up_rc_token_expired
 * @property bool $up_call_recording_disabled
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

    public const SKILL_TYPE_JUNIOR = 1;
    public const SKILL_TYPE_MIDDLE = 2;
    public const SKILL_TYPE_SENIOR = 3;

    public const SKILL_TYPE_LIST = [
        self::SKILL_TYPE_JUNIOR => 'Junior',
        self::SKILL_TYPE_MIDDLE => 'Middle',
        self::SKILL_TYPE_SENIOR => 'Senior',
    ];

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        CallAccess::flush($this->up_user_id);
    }


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
            [['up_user_id', 'up_call_type_id', 'up_skill'], 'integer'],
            [['up_user_id'], 'unique'],
            [['up_telegram_enable', 'up_auto_redial', 'up_kpi_enable', 'up_2fa_enable'], 'boolean'],
            [['up_updated_dt', 'up_join_date'], 'safe'],

            ['up_telegram', 'default', 'value' => null],
            ['up_telegram', 'string', 'max' => 20],

            [['up_2fa_secret'], 'string', 'max' => 50],
            [['up_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['up_user_id' => 'id']],

            [['up_rc_auth_token'], 'string', 'max' => 50],
            [['up_rc_user_id'], 'string', 'max' => 20],
            [['up_rc_user_password'], 'string', 'max' => 50],
            [['up_rc_token_expired'], 'safe' ],
            ['up_show_in_contact_list', 'default', 'value' => true],

            ['up_show_in_contact_list', 'default', 'value' => false],
            ['up_show_in_contact_list', 'boolean'],

            ['up_call_recording_disabled', 'default', 'value' => false],
            ['up_call_recording_disabled', 'boolean'],
        ];
    }

    public function isKpiEnable(): bool
    {
        return $this->up_kpi_enable;
    }

    public function canCall(): bool
    {
        return $this->up_call_type_id !== self::CALL_TYPE_OFF;
    }

    public function canWebCall(): bool
    {
        return $this->up_call_type_id === self::CALL_TYPE_WEB;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'up_user_id'        => 'User ID',
            'up_call_type_id'   => 'Call Type',
            'up_telegram'       => 'Telegram ID',
            'up_telegram_enable' => 'Telegram Enable',
            'up_updated_dt'     => 'Updated Dt',
            'up_auto_redial'    => 'Auto redial',
            'up_kpi_enable'     => 'KPI enable',
            'up_skill'          => 'Skill',
            'up_2fa_enable'     => '2fa enable',
            'up_2fa_secret'     => '2fa secret',
            'up_join_date'      => 'Join Date',
            'up_show_in_contact_list' => 'Show in contact list',
            'up_rc_auth_token' => 'Rocket Chat Auth Token',
            'up_rc_user_id' => 'Rocket Chat User Id',
            'up_rc_user_password' => 'Rocket Chat User Password',
            'up_rc_token_expired' => 'Rocket Chat Token Expired',
            'up_call_recording_disabled' => 'Call recording disabled',
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
        return new UserProfileQuery(static::class);
    }

    /**
     * @return bool
     */
    public function is2faEnable(): bool
    {
        return $this->up_2fa_enable;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getExperienceMonth(): int
    {
        if ($this->up_join_date && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $this->up_join_date)) {
            $currentDate = new \DateTime();
            $joinDate = new \DateTime($this->up_join_date);
            $interval = $joinDate->diff($currentDate);
            return $interval->m + ($interval->y * 12);
        }
        return 0;
    }

    /**
     * @param int $userId
     */
    public static function disableTelegramByUserId(int $userId): void
    {
        if ($profile = self::findOne(['up_user_id' => $userId])) {
            $profile->up_telegram_enable = false;
            $profile->save(false);
        }
    }

    public static function removeTelegramUser(int $userId): void
    {
        if ($profile = self::findOne(['up_user_id' => $userId])) {
            $profile->up_telegram = null;
            $profile->up_telegram_enable = false;
            $profile->save(false);
        }
    }
}

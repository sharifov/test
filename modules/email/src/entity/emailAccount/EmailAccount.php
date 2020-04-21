<?php

namespace modules\email\src\entity\emailAccount;

use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%email_account}}".
 *
 * @property int $ea_id
 * @property string $ea_email
 * @property string|null $ea_imap_settings
 * @property string|null $ea_gmail_command
 * @property string|null $ea_gmail_token
 * @property int|null $ea_protocol
 * @property string|null $ea_options
 * @property int|null $ea_active
 * @property int|null $ea_created_user_id
 * @property int|null $ea_updated_user_id
 * @property string|null $ea_created_dt
 * @property string|null $ea_updated_dt
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class EmailAccount extends \yii\db\ActiveRecord
{
    public const PROTOCOL_IMAP = 1;
    public const PROTOCOL_GMAIL_API = 2;

    public const PROTOCOL_LIST = [
        self::PROTOCOL_IMAP => 'Imap',
        self::PROTOCOL_GMAIL_API => 'Gmail Api',
    ];

    public const GMAIL_COMMAND_DELETE = 'delete';
    public const GMAIL_COMMAND_MARK_READ = 'read';
    public const GMAIL_COMMAND_NO_ACTION = 'no-action';

    public const GMAIL_COMMAND_LIST = [
        self::GMAIL_COMMAND_DELETE => self::GMAIL_COMMAND_DELETE,
        self::GMAIL_COMMAND_MARK_READ => self::GMAIL_COMMAND_MARK_READ,
        self::GMAIL_COMMAND_NO_ACTION => self::GMAIL_COMMAND_NO_ACTION,
    ];

    public function getImapSettings(): ImapSettings
    {
        return new ImapSettings($this);
    }

    public function isImapProtocol(): bool
    {
        return $this->ea_protocol === self::PROTOCOL_IMAP;
    }

    public function isGmailApiProtocol(): bool
    {
        return $this->ea_protocol === self::PROTOCOL_GMAIL_API;
    }

    public function removeGmailToken(): void
    {
        $this->ea_gmail_token = null;
    }

    public function rules(): array
    {
        return [
            ['ea_active', 'required'],
            ['ea_active', 'boolean'],

            ['ea_email', 'required'],
            ['ea_email', 'string', 'max' => 160],
            ['ea_email', 'email'],
            ['ea_email', 'unique'],

            ['ea_gmail_command', 'required'],
            ['ea_gmail_command', 'string', 'max' => 25],
            ['ea_gmail_command', 'in', 'range' => array_keys(self::GMAIL_COMMAND_LIST)],

            ['ea_gmail_token', 'string', 'max' => 65000],

            ['ea_imap_settings', 'string', 'max' => 65000],

            ['ea_options', 'string', 'max' => 65000],

            ['ea_protocol', 'required'],
            ['ea_protocol', 'integer'],
            ['ea_protocol', 'in', 'range' => array_keys(self::PROTOCOL_LIST)],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ea_created_dt', 'ea_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ea_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ea_created_user_id',
                'updatedByAttribute' => 'ea_updated_user_id',
            ],
        ];
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ea_created_user_id']);
    }

    public function getUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ea_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ea_id' => 'ID',
            'ea_email' => 'Email',
            'ea_imap_settings' => 'Imap Settings',
            'ea_gmail_command' => 'Gmail command',
            'ea_gmail_token' => 'Gmail Token',
            'ea_protocol' => 'Protocol',
            'ea_options' => 'Options',
            'ea_active' => 'Active',
            'ea_created_user_id' => 'Created User',
            'ea_updated_user_id' => 'Updated User',
            'ea_created_dt' => 'Created Dt',
            'ea_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%email_account}}';
    }
}

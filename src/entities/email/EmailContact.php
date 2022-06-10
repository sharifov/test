<?php

namespace src\entities\email;

use Yii;

/**
 * This is the model class for table "email_contact".
 *
 * @property int $ec_id
 * @property int|null $ec_email_id
 * @property int|null $ec_address_id
 * @property int $ec_type_id
 *
 * @property EmailAddress $address
 * @property Email $email
 */
class EmailContact extends \yii\db\ActiveRecord
{
    public const TYPE_FROM  = 1;
    public const TYPE_TO    = 2;
    public const TYPE_CC    = 3;
    public const TYPE_BCC   = 4;

    public const TYPE_LIST = [
        self::TYPE_FROM     => 'From',
        self::TYPE_TO       => 'To',
        self::TYPE_CC       => 'Cc',
        self::TYPE_BCC      => 'Bcc',
    ];

    public function rules(): array
    {
        return [
            ['ec_address_id', 'integer'],
            ['ec_address_id', 'exist', 'skipOnError' => true, 'targetClass' => EmailAddress::class, 'targetAttribute' => ['ec_address_id' => 'ea_id']],

            ['ec_email_id', 'integer'],
            ['ec_email_id', 'exist', 'skipOnError' => true, 'targetClass' => Email::class, 'targetAttribute' => ['ec_email_id' => 'e_id']],

            ['ec_type_id', 'integer'],
        ];
    }

    public function getAddress(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailAddress::class, ['ea_id' => 'ec_address_id']);
    }

    public function getEmail(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Email::class, ['e_id' => 'ec_email_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ec_id' => 'ID',
            'ec_email_id' => 'Email ID',
            'ec_address_id' => 'Address ID',
            'ec_type_id' => 'Type ID',
        ];
    }

    public static function tableName(): string
    {
        return 'email_contact';
    }

    /**
     * @return EmailContactQuery
     */
    public static function find(): EmailContactQuery
    {
        return new EmailContactQuery(get_called_class());
    }
}

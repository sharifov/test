<?php

namespace src\entities\email;

use src\helpers\email\MaskEmailHelper;
use src\model\BaseActiveRecord;

/**
 * This is the model class for table "email_address".
 *
 * @property int $ea_id
 * @property string $ea_email
 * @property string|null $ea_name
 *
 * @property EmailContact[] $emailContacts
 */
class EmailAddress extends BaseActiveRecord
{
    public function rules(): array
    {
        return [
            ['ea_email', 'required'],
            ['ea_email', 'string', 'max' => 160],
            ['ea_email', 'unique'],

            ['ea_name', 'string', 'max' => 100],
        ];
    }

    public function getEmailContacts(): \yii\db\ActiveQuery
    {
        return $this->hasMany(EmailContact::class, ['ec_address_id' => 'ea_id']);
    }

    public function getEmail($masking = false): ?string
    {
        return $masking ? MaskEmailHelper::masking($this->ea_email) : $this->ea_email;
    }

    public function getName(): ?string
    {
        return $this->ea_name;
    }

    public function attributeLabels(): array
    {
        return [
            'ea_id' => 'ID',
            'ea_email' => 'Email',
            'ea_name' => 'Name',
        ];
    }

    public static function tableName(): string
    {
        return 'email_address';
    }

    public static function findOrNew(string $email, ?string $name = null, $update = false): EmailAddress
    {
        $attributes = [
            'ea_email' => $email,
            'ea_name' => preg_replace('~\"(.*)\"~iU', "$1", $name),
        ];
        $address = self::findOneOrNew(['ea_email' => $email]);
        if ($address->isNewRecord || $update) {
            $address->attributes = $attributes;
            $address->save();
        }

        return $address;
    }
}

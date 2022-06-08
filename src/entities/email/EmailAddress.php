<?php

namespace src\entities\email;

use Yii;

/**
 * This is the model class for table "email_address".
 *
 * @property int $ea_id
 * @property string $ea_email
 * @property string|null $ea_name
 *
 * @property EmailContact[] $emailContacts
 */
class EmailAddress extends \yii\db\ActiveRecord
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

    public static function findOrNew(array $criteria = [], array $values = [])
    {
        $model = self::find()->where($criteria)->limit(1)->one() ?? new self($criteria);
        $model->load($values, '');

        return $model;
    }
}

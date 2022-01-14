<?php

namespace src\model\contactPhoneList\entity;

use borales\extensions\phoneInput\PhoneInputValidator;
use src\behaviors\PhoneCleanerBehavior;
use src\behaviors\UidPhoneGeneratorBehavior;
use src\model\contactPhoneData\entity\ContactPhoneData;
use src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "contact_phone_list".
 *
 * @property int $cpl_id
 * @property string $cpl_phone_number
 * @property string $cpl_uid
 * @property string|null $cpl_title
 * @property string|null $cpl_created_dt
 *
 * @property ContactPhoneData[] $contactPhoneData
 * @property ContactPhoneServiceInfo[] $contactPhoneServiceInfos
 */
class ContactPhoneList extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['cpl_phone_number', 'required'],
            ['cpl_phone_number', 'string', 'max' => 20],
            ['cpl_phone_number', 'unique'],
            ['cpl_phone_number', 'trim'],
//            [['cpl_phone_number'], PhoneInputValidator::class, 'message' => 'The format of Phone Number(' . $this->cpl_phone_number . ') is invalid.'],

            ['cpl_title', 'string', 'max' => 50],

            ['cpl_uid', 'string', 'max' => 36],

            ['cpl_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cpl_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => false,
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'phoneCleaner' => [
                'class' => PhoneCleanerBehavior::class,
                'targetColumn' => 'cpl_phone_number',
            ],
            'uidGenerator' => [
                'class' => UidPhoneGeneratorBehavior::class,
                'donorColumn' => 'cpl_phone_number',
                'targetColumn' => 'cpl_uid',
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getContactPhoneData(): \yii\db\ActiveQuery
    {
        return $this->hasMany(ContactPhoneData::class, ['cpd_cpl_id' => 'cpl_id']);
    }

    public function getContactPhoneServiceInfos(): \yii\db\ActiveQuery
    {
        return $this->hasMany(ContactPhoneServiceInfo::class, ['cpsi_cpl_id' => 'cpl_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cpl_id' => 'ID',
            'cpl_phone_number' => 'Phone Number',
            'cpl_uid' => 'Uid',
            'cpl_title' => 'Title',
            'cpl_created_dt' => 'Created',
        ];
    }

    public static function find(): ContactPhoneListScopes
    {
        return new ContactPhoneListScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'contact_phone_list';
    }

    public static function create(string $phone, ?string $title = null): ContactPhoneList
    {
        $model = new self();
        $model->cpl_phone_number = $phone;
        $model->cpl_title = $title;
        return $model;
    }
}

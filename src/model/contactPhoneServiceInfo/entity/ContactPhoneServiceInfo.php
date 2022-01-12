<?php

namespace src\model\contactPhoneServiceInfo\entity;

use common\components\validators\CheckJsonValidator;
use src\behaviors\StringToJsonBehavior;
use src\model\contactPhoneList\entity\ContactPhoneList;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "contact_phone_service_info".
 *
 * @property int $cpsi_cpl_id
 * @property int $cpsi_service_id
 * @property string|null $cpsi_data_json
 * @property string|null $cpsi_created_dt
 * @property string|null $cpsi_updated_dt
 *
 * @property ContactPhoneList $cpsiCpl
 */
class ContactPhoneServiceInfo extends \yii\db\ActiveRecord
{
    public const SERVICE_APP = 1;
    public const SERVICE_NEUTRINO = 2;
    public const SERVICE_TWILIO = 3;

    public const SERVICE_LIST = [
        self::SERVICE_APP => 'App',
        self::SERVICE_NEUTRINO => 'Neutrino',
        self::SERVICE_TWILIO => 'Twilio',
    ];

    public function rules(): array
    {
        return [
            [['cpsi_cpl_id', 'cpsi_service_id'], 'unique', 'targetAttribute' => ['cpsi_cpl_id', 'cpsi_service_id']],

            ['cpsi_cpl_id', 'required'],
            ['cpsi_cpl_id', 'integer'],
            ['cpsi_cpl_id', 'exist', 'skipOnError' => true, 'targetClass' => ContactPhoneList::class, 'targetAttribute' => ['cpsi_cpl_id' => 'cpl_id']],

            ['cpsi_data_json', CheckJsonValidator::class],

            ['cpsi_service_id', 'required'],
            ['cpsi_service_id', 'integer'],
            ['cpsi_service_id', 'in', 'range' => array_keys(self::SERVICE_LIST)],

            [['cpsi_created_dt', 'cpsi_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cpsi_created_dt', 'cpsi_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cpsi_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'stringToJson' => [
                'class' => StringToJsonBehavior::class,
                'jsonColumn' => 'cpsi_data_json',
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCpsiCpl(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ContactPhoneList::class, ['cpl_id' => 'cpsi_cpl_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cpsi_cpl_id' => 'Cpl ID',
            'cpsi_service_id' => 'Service ID',
            'cpsi_data_json' => 'Data Json',
            'cpsi_created_dt' => 'Created',
            'cpsi_updated_dt' => 'Updated',
        ];
    }

    public static function find(): ContactPhoneServiceInfoScopes
    {
        return new ContactPhoneServiceInfoScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'contact_phone_service_info';
    }

    public static function getServiceName(?int $serviceId): string
    {
        return self::SERVICE_LIST[$serviceId] ?? '-';
    }

    public static function create(
        int $cplId,
        int $serviceId,
        $dataJson
    ): ContactPhoneServiceInfo {
        $model = new self();
        $model->cpsi_cpl_id = $cplId;
        $model->cpsi_service_id = $serviceId;
        $model->cpsi_data_json = $dataJson;

        return $model;
    }
}

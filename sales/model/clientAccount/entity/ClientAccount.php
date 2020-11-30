<?php

namespace sales\model\clientAccount\entity;

use common\models\Client;
use common\models\Currency;
use common\models\Language;
use common\models\Project;
use sales\model\clientAccountSocial\entity\ClientAccountSocial;
use thamtech\uuid\validators\UuidValidator;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client_account".
 *
 * @property int $ca_id
 * @property int|null $ca_project_id
 * @property string $ca_uuid
 * @property int $ca_hid
 * @property string $ca_username
 * @property string $ca_first_name
 * @property string|null $ca_middle_name
 * @property string|null $ca_last_name
 * @property string|null $ca_nationality_country_code
 * @property string|null $ca_dob
 * @property int|null $ca_gender
 * @property string|null $ca_phone
 * @property int|null $ca_subscription
 * @property string|null $ca_language_id
 * @property string|null $ca_currency_code
 * @property string|null $ca_timezone
 * @property string|null $ca_created_ip
 * @property int|null $ca_enabled
 * @property string|null $ca_origin_created_dt
 * @property string|null $ca_origin_updated_dt
 * @property string|null $ca_created_dt
 * @property string|null $ca_updated_dt
 * @property string|null $ca_email
 *
 * @property Currency $currency
 * @property Language $language
 * @property Project $project
 * @property ClientAccountSocial[] $clientAccountSocials
 * @property Client[] $clients
 */
class ClientAccount extends ActiveRecord
{
    public const GENDER_MAN = 1;
    public const GENDER_WOMAN = 2;

    public const GENDER_LIST = [
        self::GENDER_MAN => 'Man',
        self::GENDER_WOMAN => 'Woman',
    ];

    public function rules(): array
    {
        return [
            ['ca_created_ip', 'string', 'max' => 40],

            ['ca_currency_code', 'string', 'max' => 3],
            ['ca_currency_code', 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['ca_currency_code' => 'cur_code']],
            ['ca_currency_code', 'default', 'value' => null],

            ['ca_enabled', 'boolean'],

            ['ca_first_name', 'string', 'max' => 100],

            ['ca_gender', 'integer'],
            ['ca_gender', 'in', 'range' => array_keys(self::GENDER_LIST)],

            'ca_hid_required' => ['ca_hid', 'required'],
            ['ca_hid', 'integer'],

            ['ca_language_id', 'string', 'max' => 5],
            ['ca_language_id', 'exist', 'skipOnError' => true, 'targetClass' => Language::class, 'targetAttribute' => ['ca_language_id' => 'language_id']],
            ['ca_language_id', 'default', 'value' => null],

            ['ca_last_name', 'string', 'max' => 100],

            ['ca_middle_name', 'string', 'max' => 100],

            ['ca_nationality_country_code', 'string', 'max' => 2],

            ['ca_phone', 'string', 'max' => 100],

            ['ca_project_id', 'integer'],
            ['ca_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['ca_project_id' => 'id']],

            ['ca_subscription', 'boolean'],

            ['ca_timezone', 'string', 'max' => 50],

            'ca_username_required' => ['ca_username', 'required'],
            ['ca_username', 'string', 'max' => 100],

            ['ca_email', 'string', 'max' => 100],
            ['ca_email', 'email'],

            'ca_uuid_required' => ['ca_uuid', 'required'],
            ['ca_uuid', 'string', 'max' => 36],
            ['ca_uuid', 'unique'],
            ['ca_uuid', UuidValidator::class],

            'ca_created_dt_format' => ['ca_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            'ca_updated_dt_format' => ['ca_updated_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['ca_origin_created_dt', 'safe'],

            ['ca_origin_updated_dt', 'safe'],

            ['ca_dob', 'safe'],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ca_created_dt', 'ca_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ca_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    public function getCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'ca_currency_code']);
    }

    public function getLanguage(): ActiveQuery
    {
        return $this->hasOne(Language::class, ['language_id' => 'ca_language_id']);
    }

    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'ca_project_id']);
    }

    public function getClientAccountSocials(): ActiveQuery
    {
        return $this->hasMany(ClientAccountSocial::class, ['cas_ca_id' => 'ca_id']);
    }

    public function getClients(): ActiveQuery
    {
        return $this->hasMany(Client::class, ['c_ca_id' => 'ca_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ca_id' => 'ID',
            'ca_project_id' => 'Project',
            'ca_uuid' => 'Uuid',
            'ca_hid' => 'Hid',
            'ca_username' => 'Username',
            'ca_first_name' => 'First Name',
            'ca_middle_name' => 'Middle Name',
            'ca_last_name' => 'Last Name',
            'ca_nationality_country_code' => 'Country Code',
            'ca_dob' => 'Dob',
            'ca_gender' => 'Gender',
            'ca_phone' => 'Phone',
            'ca_subscription' => 'Subscription',
            'ca_language_id' => 'Language',
            'ca_currency_code' => 'Currency Code',
            'ca_timezone' => 'Timezone',
            'ca_created_ip' => 'Created Ip',
            'ca_enabled' => 'Enabled',
            'ca_origin_created_dt' => 'Origin Created Dt',
            'ca_origin_updated_dt' => 'Origin Updated Dt',
            'ca_created_dt' => 'Created Dt',
            'ca_updated_dt' => 'Updated Dt',
            'ca_email' => 'Email',
        ];
    }

    public static function find(): ClientAccountScopes
    {
        return new ClientAccountScopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%client_account}}';
    }
}

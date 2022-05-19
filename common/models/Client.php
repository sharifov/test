<?php

namespace common\models;

use common\components\Metrics;
use common\models\query\ClientQuery;
use src\behaviors\metric\MetricClientCounterBehavior;
use src\entities\cases\Cases;
use src\entities\EventTrait;
use src\helpers\phone\MaskPhoneHelper;
use src\model\client\entity\events\ClientChangeIpEvent;
use src\model\client\entity\events\ClientCreatedEvent;
use src\model\client\entity\events\ClientExcludedEvent;
use src\model\clientAccount\entity\ClientAccount;
use src\model\clientData\entity\ClientData;
use src\model\clientVisitor\entity\ClientVisitor;
use src\model\coupon\entity\couponClient\CouponClient;
use thamtech\uuid\helpers\UuidHelper;
use thamtech\uuid\validators\UuidValidator;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "clients".
 *
 * @property int $id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string $created
 * @property string $updated
 * @property string $uuid
 * @property string $full_name
 * @property int $parent_id
 * @property bool $is_company
 * @property bool $is_public
 * @property string $company_name
 * @property string $description
 * @property bool $disabled
 * @property int $rating
 * @property int $cl_type_id // 1 - Client, 2 - Contact
 * @property int|null $cl_type_create // 1 - Manually, 2 - Lead etc.
 * @property int|null $cl_project_id
 * @property int|null $cl_ca_id
 * @property bool $cl_excluded
 * @property string|null $cl_ppn
 * @property string|null $cl_ip
 * @property string|null $cl_locale
 * @property string|null $cl_marketing_country
 * @property bool $cl_call_recording_disabled
 *
 * @property ClientEmail[] $clientEmails
 * @property ClientPhone[] $clientPhones
 * @property ClientPhone[] $clientPhonesByType
 * @property ClientProject[] $clientProjects
 * @property Lead[] $leads
 * @property Lead[] $leadsLimited
 * @property Cases[] $cases
 * @property string $nameByType
 * @property array $phoneNumbersSms
 * @property array $emailList
 * @property Project[] $projects
 * @property UserContactList $contact
 * @property Project|null $project
 * @property ClientAccount|null $clientAccount
 * @property ClientVisitor|null $clientVisitor
 * @property CouponClient[] $couponClient
 * @property ClientData[] $clientData
 * @property string lastClientEmail
 *
 * @method clientPhonesByType(array $array)
 */
class Client extends ActiveRecord
{
    use EventTrait;

    public const SCENARIO_MANUALLY = 'manually';

    public $full_name;

    public const TYPE_CLIENT  = 1;
    public const TYPE_CONTACT = 2;
    public const TYPE_INTERNAL = 3;

    public const TYPE_LIST = [
        self::TYPE_CLIENT  => 'Client',
        self::TYPE_CONTACT => 'Contact',
        self::TYPE_INTERNAL => 'Internal',
    ];

    public const TYPE_CREATE_MANUALLY = 1;
    public const TYPE_CREATE_LEAD = 2;
    public const TYPE_CREATE_CASE = 3;
    public const TYPE_CREATE_CALL = 4;
    public const TYPE_CREATE_SMS = 5;
    public const TYPE_CREATE_EMAIL = 6;
    public const TYPE_CREATE_CLIENT_CHAT = 7;
    public const TYPE_CREATE_CLIENT_ACCOUNT = 8;

    public const TYPE_CREATE_LIST = [
        self::TYPE_CREATE_MANUALLY => 'Manually',
        self::TYPE_CREATE_LEAD => 'Lead',
        self::TYPE_CREATE_CASE => 'Case',
        self::TYPE_CREATE_CALL => 'Call',
        self::TYPE_CREATE_SMS => 'Sms',
        self::TYPE_CREATE_EMAIL => 'Email',
        self::TYPE_CREATE_CLIENT_CHAT => 'Client chat',
        self::TYPE_CREATE_CLIENT_ACCOUNT => 'Client account',
    ];

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%clients}}';
    }

    /**
     * @param $firstName
     * @param $middleName
     * @param $lastName
     * @param $projectId
     * @param $typeCreate
     * @param $parentId
     * @param $ip
     * @return Client
     */
    public static function create($firstName, $middleName, $lastName, $projectId, $typeCreate, $parentId, $ip): self
    {
        $client = new static();
        $client->first_name = $firstName;
        $client->middle_name = $middleName;
        $client->last_name = $lastName;
        $client->cl_project_id = $projectId;
        $client->cl_type_create = $typeCreate;
        $client->parent_id = $parentId;
        $client->uuid = UuidHelper::uuid();
        $client->cl_ip = $ip;
        $client->recordEvent(new ClientCreatedEvent($client));
        return $client;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $middleName
     * @param string|null $locale
     * @param string|null $country
     */
    public function edit(
        string $firstName,
        string $lastName,
        string $middleName,
        ?string $locale = null,
        ?string $country = null
    ): void {
        $this->first_name = $firstName;
        $this->last_name = $lastName;
        $this->middle_name = $middleName;
        $this->cl_locale = $locale;
        $this->cl_marketing_country = strtoupper($country);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['created', 'updated', 'ucl_favorite',], 'safe'],
            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 100],
            [['company_name'], 'string', 'max' => 150],
            [['description'], 'string'],
            [['is_company', 'is_public', 'disabled'], 'boolean'],
            [['parent_id', 'rating', 'cl_type_id'], 'integer'],
            ['uuid', 'unique'],
            ['uuid', UuidValidator::class],

            ['cl_type_create', 'integer'],
            ['cl_type_create', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['cl_type_create', 'in', 'range' => array_keys(self::TYPE_CREATE_LIST)],

            ['cl_project_id', 'required', 'on' => [self::SCENARIO_MANUALLY]],
            ['cl_project_id', 'integer'],
            ['cl_project_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['cl_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['cl_project_id' => 'id']],

            ['cl_ca_id', 'integer'],
            ['cl_ca_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['cl_ca_id', 'exist', 'skipOnError' => true, 'targetClass' => ClientAccount::class, 'targetAttribute' => ['cl_ca_id' => 'ca_id']],

            ['cl_excluded', 'default', 'value' => false],
            ['cl_excluded', 'boolean'],

            ['cl_ppn', 'default', 'value' => null],
            ['cl_ppn', 'string', 'max' => 10],

            ['cl_ip', 'string', 'max' => 39],

            ['cl_locale', 'string', 'max' => 5],

            ['cl_marketing_country', 'string', 'max' => 2],
            ['cl_marketing_country', 'filter', 'filter' => 'strtoupper', 'skipOnEmpty' => true],

            ['cl_call_recording_disabled', 'default', 'value' => false],
            ['cl_call_recording_disabled', 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'created' => 'Created',
            'updated' => 'Updated',
            'full_name' => 'Full Name',
            'parent_id' => 'Parent id',
            'is_company' => 'Is company',
            'is_public' => 'Is public',
            'company_name' => 'Company name',
            'description' => 'Description',
            'disabled' => 'Disabled',
            'rating' => 'Rating',
            'cl_type_id' => 'Type',
            'cl_type_create' => 'Type create',
            'cl_project_id' => 'Project',
            'cl_ca_id' => 'Client account',
            'cl_excluded' => 'Is exclude',
            'cl_ppn' => 'PPN',
            'cl_ip' => 'IP',
            'cl_locale' => 'Locale',
            'cl_marketing_country' => 'Market country',
            'cl_call_recording_disabled' => 'Call recording disabled',
        ];
    }

    public function afterFind(): void
    {
        parent::afterFind();
        $this->full_name = trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * @return ActiveQuery
     */
    public function getClientEmails(): ActiveQuery
    {
        return $this->hasMany(ClientEmail::class, ['client_id' => 'id']);
    }

    public function getOnlyEmails(): array
    {
        return $this->hasMany(ClientEmail::class, ['client_id' => 'id'])->select('email')->asArray()->column();
    }

    public function getOnlyPhones(): array
    {
        return $this->hasMany(ClientPhone::class, ['client_id' => 'id'])->select('phone')->asArray()->column();
    }

    public function getLeadIdsAndRequestIp(int $limit = 5): array
    {
        return $this->hasMany(Lead::class, ['client_id' => 'id'])->select(['id', 'request_ip'])->limit($limit)->asArray()->all();
    }

    public function leadsCountByClient(): int
    {
        return $this->hasMany(Lead::class, ['client_id' => 'id'])->count();
    }


    /**
     * @return ActiveQuery
     */
    public function getClientEmailsLimited(int $limit = 30): ActiveQuery
    {
        return $this->hasMany(ClientEmail::class, ['client_id' => 'id'])->limit($limit);
    }

    /**
     * @param array $types
     * @return array
     */
    public function getClientEmailsByType(array $types = []): array
    {
        return $this->hasMany(ClientEmail::class, ['client_id' => 'id'])->select(['email'])->andFilterWhere(['IN', 'type', $types])->column();
    }

    /**
     * @return ActiveQuery
     */
    public function getClientPhones(): ActiveQuery
    {
        return $this->hasMany(ClientPhone::class, ['client_id' => 'id']);
    }

    /**
     * @param array $types
     * @return array
     */
    public function getClientPhonesByType(array $types = []): array
    {
        return $this->hasMany(ClientPhone::class, ['client_id' => 'id'])->select(['phone'])->andFilterWhere(['IN', 'type', $types])->column();
    }

    /**
     * @return ActiveQuery
     */
    public function getLeads(): ActiveQuery
    {
        return $this->hasMany(Lead::class, ['client_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLeadsLimited(int $limit = 30): ActiveQuery
    {
        return $this->hasMany(Lead::class, ['client_id' => 'id'])->limit($limit);
    }

    /**
     * @return ActiveQuery
     */
    public function getCases(): ActiveQuery
    {
        return $this->hasMany(Cases::class, ['cs_client_id' => 'id']);
    }


    /**
     * @return ActiveQuery
     */
    public function getCasesLimited(int $limit = 30): ActiveQuery
    {
        return $this->hasMany(Cases::class, ['cs_client_id' => 'id'])->limit($limit);
    }

    /**
     * @return ActiveQuery
     */
    public function getProjects(): ActiveQuery
    {
        return $this->hasMany(Project::class, ['id' => 'cp_project_id'])->viaTable('client_project', ['cp_client_id' => 'id']);
    }

    /**
     * Gets query for [[ClientProjects]].
     *
     * @return ActiveQuery
     */
    public function getClientProjects()
    {
        return $this->hasMany(ClientProject::class, ['cp_client_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContact(): ActiveQuery
    {
        return $this->hasOne(UserContactList::class, ['ucl_client_id' => 'id']);
    }

    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'cl_project_id']);
    }

    public function getClientAccount(): ActiveQuery
    {
        return $this->hasOne(ClientAccount::class, ['ca_id' => 'cl_ca_id']);
    }

    public function getClientVisitor(): ActiveQuery
    {
        return $this->hasOne(ClientVisitor::class, ['cv_client_id' => 'id']);
    }

    public function getCouponClient(): ActiveQuery
    {
        return $this->hasMany(CouponClient::class, ['cuc_client_id' => 'id']);
    }

    public function getClientData(): ActiveQuery
    {
        return $this->hasMany(ClientData::class, ['cd_client_id' => 'id']);
    }

    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if (!$this->uuid) {
            $this->uuid = UuidHelper::uuid();
        }

        return true;
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created', 'updated'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'metric' => [
                'class' => MetricClientCounterBehavior::class,
            ],
        ];
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\base\InvalidConfigException
     */
    public function afterSave($insert, $changedAttributes)
    {
        //      $newAttr = [];
//      foreach ($changedAttributes as $key => $attribute) {
//          if (array_key_exists($key, $this->oldAttributes)) {
//              $newAttr[$key] = $this->oldAttributes[$key];
//          }
//      }
//
//      $newAttr = json_encode($newAttr);
//      $oldAttr = json_encode($changedAttributes);
//
//      $log = \Yii::createObject(GlobalLogInterface::class);
//
//      $log->log(
//          new LogDTO(
//              self::class,
//              $this->oldAttributes['id'] ?? null,
//              \Yii::$app->params['appName'] ?? '',
//              \Yii::$app->user->id,
//              $oldAttr,
//              $newAttr,
//              null
//          )
//      );
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        $data = self::find()->orderBy(['id' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'id', 'first_name');
    }

    /**
     * @return array
     */
    public static function getParentList(): array
    {
        $data = self::find()->where(['IS', 'parent_id', null])->orderBy(['id' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'id', 'first_name');
    }

    /**
     * @return array
     */
    public function getPhoneNumbersSms($maskState = false): array
    {
        $phoneList = [];
        $phones = $this->clientPhones;
        if ($phones) {
            foreach ($phones as $phone) {
                $phoneList[$phone->phone] = MaskPhoneHelper::masking($phone->phone, $maskState) . ($phone->is_sms ? ' (sms)' : '');
            }
        }
        return $phoneList;
    }

    public function getLastClientEmail(): string
    {
        $emails = $this->emailList;
        return strval(end($emails));
    }

    /**
     * @return array
     */
    public function getEmailList(): array
    {
        return ArrayHelper::map($this->clientEmails, 'email', 'email');
    }

    /**
     * @return ClientQuery
     */
    public static function find(): ClientQuery
    {
        return new ClientQuery(static::class);
    }

    /**
     * @return string
     */
    public function getNameByType(): string
    {
        return $this->is_company ?
            $this->company_name :
            trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function getAvatar(): string
    {
        return strtoupper($this->getNameByType()[0] ?? '');
    }

    public function getFullName(): string
    {
        return trim($this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name);
    }

    public function getShortName(): string
    {
        return $this->is_company ?
            $this->company_name :
            trim($this->first_name . ' ' . $this->last_name);
    }

    public function isClient(): bool
    {
        return $this->cl_type_id === self::TYPE_CLIENT;
    }

    public function isContact(): bool
    {
        return $this->cl_type_id === self::TYPE_CONTACT;
    }

    public function isInternal(): bool
    {
        return $this->cl_type_id === self::TYPE_INTERNAL;
    }

    public function isProjectEqual(int $projectId): bool
    {
        return $this->cl_project_id === $projectId;
    }

    public function isWithoutProject(): bool
    {
        return $this->cl_project_id === null;
    }

    public static function createByClientAccount(ClientAccount $clientAccount): self
    {
        $client = new static();
        $client->first_name = $clientAccount->ca_first_name;
        $client->middle_name = $clientAccount->ca_middle_name;
        $client->last_name = $clientAccount->ca_last_name;
        $client->cl_project_id = $clientAccount->ca_project_id;
        $client->uuid = $clientAccount->ca_uuid;
        $client->cl_ca_id = $clientAccount->ca_id;
        $client->cl_type_create = self::TYPE_CREATE_CLIENT_ACCOUNT;
        return $client;
    }

    public function isExcluded(): bool
    {
        return $this->cl_excluded ? true : false;
    }

    public function exclude(string $ppn): void
    {
        $this->cl_excluded = true;
        $this->cl_ppn = $ppn;
        $this->recordEvent(new ClientExcludedEvent($this->id));
    }

    public function getTypeCreateName(): string
    {
        return self::TYPE_CREATE_LIST[$this->cl_type_create] ?? 'Undefined';
    }

    /**
     * @param string $ip
     */
    public function changeIp(string $ip): void
    {
        $this->cl_ip = $ip;
        $this->recordEvent(new ClientChangeIpEvent($this));
    }
}

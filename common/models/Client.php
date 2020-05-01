<?php

namespace common\models;

use common\models\query\ClientQuery;
use sales\entities\EventTrait;
use sales\logger\db\GlobalLogInterface;
use sales\logger\db\LogDTO;
use thamtech\uuid\helpers\UuidHelper;
use thamtech\uuid\validators\UuidValidator;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

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
 *
 * @property ClientEmail[] $clientEmails
 * @property ClientPhone[] $clientPhones
 * @property ClientPhone[] $clientPhonesByType
 * @property Lead[] $leads
 * @property string $nameByType
 * @property array $phoneNumbersSms
 * @property array $emailList
 * @property Project[] $projects
 * @property UserContactList $contact
 * @method clientPhonesByType(array $array)
 */
class Client extends ActiveRecord
{
    use EventTrait;

    public $full_name;

    public const TYPE_CLIENT  = 1;
    public const TYPE_CONTACT = 2;

    public const TYPE_LIST = [
        self::TYPE_CLIENT  => 'Client',
        self::TYPE_CONTACT => 'Contact',
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
     * @return Client
     */
    public static function create($firstName, $middleName, $lastName): self
    {
        $client = new static();
        $client->first_name = $firstName;
        $client->middle_name = $middleName;
        $client->last_name = $lastName;
        $client->uuid = UuidHelper::uuid();
        return $client;
    }

	/**
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $middleName
	 */
	public function edit(string $firstName, string $lastName, string $middleName): void
	{
		$this->first_name = $firstName;
		$this->last_name = $lastName;
		$this->middle_name = $middleName;
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
            'ucl_favorite' => 'Favorite'
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
    public function getProjects(): ActiveQuery
    {
         return $this->hasMany(Project::class, ['id' => 'cp_project_id'])->viaTable('client_project', ['cp_client_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getContact(): ActiveQuery
    {
         return $this->hasOne(UserContactList::class, ['ucl_client_id' => 'id']);
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
				'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
			],
		];
	}

//    public function beforeSave($insert): bool
//    {
//        if (parent::beforeSave($insert)) {
//
//            if ($insert) {
//                if (!$this->created) {
//                    $this->created = date('Y-m-d H:i:s');
//                }
//            }
//
//            $this->updated = date('Y-m-d H:i:s');
//            return true;
//        }
//        return false;
//    }

	/**
	 * @param bool $insert
	 * @param array $changedAttributes
	 * @throws \yii\base\InvalidConfigException
	 */
    public function afterSave($insert, $changedAttributes)
	{
//		$newAttr = [];
//		foreach ($changedAttributes as $key => $attribute) {
//			if (array_key_exists($key, $this->oldAttributes)) {
//				$newAttr[$key] = $this->oldAttributes[$key];
//			}
//		}
//
//		$newAttr = json_encode($newAttr);
//		$oldAttr = json_encode($changedAttributes);
//
//		$log = \Yii::createObject(GlobalLogInterface::class);
//
//		$log->log(
//			new LogDTO(
//				self::class,
//				$this->oldAttributes['id'] ?? null,
//				\Yii::$app->params['appName'] ?? '',
//				\Yii::$app->user->id,
//				$oldAttr,
//				$newAttr,
//				null
//			)
//		);
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
    public function getPhoneNumbersSms(): array
    {
        $phoneList = [];
        $phones = $this->clientPhones;
        if ($phones) {
            foreach ($phones as $phone) {
                $phoneList[$phone->phone] = $phone->phone . ($phone->is_sms ? ' (sms)' : '');
            }
        }
        return $phoneList;
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
            trim($this->first_name . ' ' . $this->first_name . ' ' . $this->last_name);
    }

    public function getAvatar(): string
    {
        return strtoupper($this->getNameByType()[0] ?? '');
    }
}

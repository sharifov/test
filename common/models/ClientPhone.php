<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use sales\entities\EventTrait;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\VarDumper;
use yii\queue\Queue;
use common\components\CheckPhoneNumberJob;


/**
 * This is the model class for table "client_phone".
 *
 * @property int $id
 * @property int $client_id
 * @property string $phone
 * @property int $is_sms
 * @property string $validate_dt
 * @property string $created
 * @property string $updated
 * @property string $comments
 * @property string $type
 *
 * @property Client $client
 */
class ClientPhone extends \yii\db\ActiveRecord
{

    use EventTrait;

    public const PHONE_VALID = 1;
    public const PHONE_FAVORITE = 2;
    public const PHONE_INVALID = 9;
    public const PHONE_NOT_SET = 0;

    public const PHONE_TYPE = [
		self::PHONE_NOT_SET => 'Not set',
    	self::PHONE_VALID => 'Valid',
		self::PHONE_FAVORITE => 'Favorite',
		self::PHONE_INVALID => 'Invalid',
	];

    public const PHONE_TYPE_ICONS = [
		self::PHONE_VALID => '<i class="fa fa-phone success"></i> ',
		self::PHONE_FAVORITE => '<i class="fa fa-phone warning"></i> ',
		self::PHONE_INVALID => '<i class="fa fa-phone danger"></i> ',
		self::PHONE_NOT_SET => '<i class="fa fa-phone"></i> '
	];

    public const PHONE_TYPE_LABELS = [
    	self::PHONE_VALID => '<span class="label label-success">{type}</span>',
		self::PHONE_FAVORITE => '<span class="label label-warning">{type}</span>',
		self::PHONE_INVALID => '<span class="label label-danger">{type}</span>',
		self::PHONE_NOT_SET => '<span class="label label-primary">{type}</span>'
	];

	public const PHONE_TYPE_TEXT_DECORATION = [
		self::PHONE_INVALID => 'text-line-through'
	];

    // old phone value. need for afterSave() method
    private $old_phone = '';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_phone';
    }

	/**
	 * @param string $phone
	 * @param int $clientId
	 * @param int|null $phoneType
	 * @param string|null $comments
	 * @return ClientPhone
	 */
    public static function create(string $phone, int $clientId, int $phoneType = null, string $comments = null): self
    {
        $clientPhone = new static();
        $clientPhone->phone = $phone;
        $clientPhone->client_id = $clientId;
        $clientPhone->type = $phoneType;
        $clientPhone->comments = $comments;
        return $clientPhone;
    }

	/**
	 * @param string $phone
	 * @param int|null $phoneType
	 */
    public function edit(string $phone, int $phoneType = null): void
	{
		$this->phone = $phone;
		$this->type = $phoneType;
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['client_id', 'is_sms', 'type'], 'integer'],
            [['created', 'updated', 'comments', 'validate_dt'], 'safe'],

            [['phone'], 'string', 'max' => 20],
            [['phone'], PhoneInputValidator::class],

            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['client_id' => 'id']],
            [['phone', 'client_id'], 'unique', 'targetAttribute' => ['phone', 'client_id']],

            ['type', 'in', 'range' => array_keys(self::PHONE_TYPE)]
        ];
    }

	/**
	 * @return array
	 */
    public function formatValue(): array
	{
		return [
			'type' => static function ($value) {
				return self::PHONE_TYPE[$value];
			}
		];
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'phone' => 'Phone',
            'is_sms' => 'Can send SMS',
            'validate_dt' => 'Validated at',
            'created' => 'Created',
            'updated' => 'Updated',
			'type' => 'Phone Type'
        ];
    }

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


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::class, ['id' => 'client_id']);
    }

    public function beforeValidate()
    {
        $this->phone = str_replace('-', '', $this->phone);
        $this->phone = str_replace(' ', '', $this->phone);
        if(!$this->isNewRecord) {
            $this->old_phone = $this->oldAttributes['phone'];
        }
        $this->updated = date('Y-m-d H:i:s');
        return parent::beforeValidate();
    }

    /**
     * @param string $phoneNumber
     * @return null|string|string[]
     */
    public static function clearNumber(string $phoneNumber = '')
    {
        $phoneNumber = preg_replace('~[^0-9\+]~', '', $phoneNumber);
        if(isset($phoneNumber[0])) {
            $phoneNumber = ($phoneNumber[0] === '+' ? '+' : '') . str_replace('+', '', $phoneNumber);
        }
        return $phoneNumber;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if($this->id > 0 && $this->client_id > 0 ) {
            $isRenewPhoneNumber = ( $this->old_phone != '' && $this->old_phone !== $this->phone );
            /*\Yii::info(VarDumper::dumpAsString([
                'client_id' => $this->client_id,
                'id' => $this->id,
                'validate_dt' => $this->validate_dt,
                'is_sms' => $this->is_sms,
                'old_phone' => $this->old_phone,
                'phone' => $this->phone,
                'isRenewPhoneNumber' => $isRenewPhoneNumber,
            ]), 'info\model:ClientPhone:afterSave');*/

            // check if phone rewrite
            if(NULL === $this->validate_dt || $isRenewPhoneNumber) {
                /** @var Queue $queue */
                $queue = \Yii::$app->queue_phone_check;
                $job = new CheckPhoneNumberJob();
                $job->client_id = $this->client_id;
                $job->client_phone_id = $this->id;
                $queue->push($job);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

	/**
	 * @return int
	 */
	public function countUsersSamePhone(): int
	{
		$subQuery = (new Query())->select(['client_id'])->distinct()
			->from(ClientPhone::tableName())
			->where(['phone' => $this->phone]);

		$query = (new Query())->select(['id'])->distinct()
			->from(Client::tableName())
			->where(['NOT IN', 'id', $this->client_id])
			->andWhere(['IN', 'id', $subQuery]);

		return (int)$query->count();
	}

	/**
	 * @param int|null $type
	 * @return mixed|string
	 */
	public static function getPhoneType(?int $type): string
	{
		return self::PHONE_TYPE[$type] ?? '';
	}

	/**
	 * @return array
	 */
	public static function getPhoneTypeList(): array
	{
		return self::PHONE_TYPE;
	}

	/**
	 * @param int $type
	 * @return mixed|string
	 */
	public static function getPhoneTypeTextDecoration(?int $type): string
	{
		return self::PHONE_TYPE_TEXT_DECORATION[$type] ?? '';
	}

	/**
	 * @param int $type
	 * @return mixed|string
	 */
	public static function getPhoneTypeIcon(?int $type): string
	{
		return self::PHONE_TYPE_ICONS[$type] ?? '';
	}

	/**
	 * @param int|null $type
	 * @return string
	 */
	public static function getPhoneTypeLabel(?int $type): string
	{
		if (isset(self::PHONE_TYPE_LABELS[$type], self::PHONE_TYPE[$type])) {
			return str_replace('{type}', self::PHONE_TYPE[$type], self::PHONE_TYPE_LABELS[$type]);
		}
		return '';
	}

    /**
     * @param int $clientId
     * @param int $excludeTypes
     * @return array
     */
    public static function getPhoneListByClient(int $clientId, array $excludeTypes = [self::PHONE_INVALID]): array
    {
        return (new Query())->select(['phone'])->distinct()
			->from(self::tableName())
			->where(['client_id' => $clientId])
			->andWhere(['NOT IN', 'type', $excludeTypes])
			->orWhere(['AND', ['type' => null], ['client_id' => $clientId]])
			->column();
    }
}

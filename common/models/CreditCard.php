<?php

namespace common\models;

use common\models\query\CreditCardQuery;
use src\helpers\payment\CreditCardHelper;
use yii\base\Exception;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "credit_card".
 *
 * @property int $cc_id
 * @property string $cc_number
 * @property string|null $cc_display_number
 * @property string|null $cc_holder_name
 * @property string|null $cc_security_hash
 * @property int $cc_expiration_month
 * @property int $cc_expiration_year
 * @property string|null $cc_cvv
 * @property int|null $cc_type_id
 * @property int|null $cc_status_id
 * @property int|null $cc_is_expired
 * @property int|null $cc_created_user_id
 * @property int|null $cc_updated_user_id
 * @property string|null $cc_created_dt
 * @property string|null $cc_updated_dt
 * @property string|null $cc_bo_link
 * @property int|null $cc_is_sync_bo
 *
 * @property BillingInfo[] $billingInfos
 * @property Employee $ccCreatedUser
 * @property string $typeName
 * @property string $statusLabel
 * @property string $className
 * @property string $statusName
 * @property string $securityKey
 * @property string $initNumber
 * @property string $initCvv
 * @property Employee $ccUpdatedUser
 */
class CreditCard extends ActiveRecord
{
    public const TYPE_VISA              =   1;
    public const TYPE_MASTER_CARD       =   2;
    public const TYPE_AMERICAN_EXPRESS  =   3;
    public const TYPE_DISCOVER          =   4;
    public const TYPE_DINERS_CLUB       =   5;
    public const TYPE_JCB               =   6;


    public const TYPE_LIST = [
        self::TYPE_VISA                =>   'Visa',
        self::TYPE_MASTER_CARD         =>   'Master Card',
        self::TYPE_AMERICAN_EXPRESS    =>   'American Express',
        self::TYPE_DISCOVER            =>   'Discover',
        self::TYPE_DINERS_CLUB         =>   'Diners Club',
        self::TYPE_JCB                 =>   'JCB'
    ];

    public const STATUS_VALID          =   1;
    public const STATUS_INVALID        =   2;

    public const STATUS_LIST = [
        self::STATUS_VALID           =>   'Valid',
        self::STATUS_INVALID         =>   'Invalid',
    ];

    public const STATUS_CLASS_LIST = [
        self::STATUS_VALID           =>   'success',
        self::STATUS_INVALID         =>   'danger',
    ];

    public const SCENARIO_CASE_AJAX_UPDATE = 'case_ajax_update';
    public const SCENARIO_WITHOUT_PRIVATE_DATA = 'without_private_data';

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'credit_card';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_WITHOUT_PRIVATE_DATA] = $scenarios[self::SCENARIO_DEFAULT];
        $scenarios[self::SCENARIO_CASE_AJAX_UPDATE] = ['cc_holder_name', 'cc_type_id'];
        return $scenarios;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['cc_number', 'required', 'on' => self::SCENARIO_DEFAULT],
            ['cc_number', 'string', 'max' => 50],

            ['cc_cvv', 'string', 'skipOnEmpty' => true],
            ['cc_cvv', 'string', 'max' => 32],

            [['cc_expiration_month', 'cc_expiration_year'], 'required'],
            [['cc_expiration_month', 'cc_expiration_year', 'cc_type_id', 'cc_status_id', 'cc_is_expired', 'cc_created_user_id', 'cc_updated_user_id'], 'integer'],
            [['cc_created_dt', 'cc_updated_dt'], 'safe'],
            [['cc_is_sync_bo'], 'integer'],
            [['cc_display_number'], 'string', 'max' => 18],
            [['cc_holder_name'], 'string', 'max' => 50],
            [['cc_bo_link'], 'string', 'max' => 255],
            ['cc_security_hash', 'string', 'max' => 32],
            [['cc_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cc_created_user_id' => 'id']],
            [['cc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['cc_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cc_id' => 'ID',
            'cc_number' => 'Number',
            'cc_display_number' => 'Display Number',
            'cc_holder_name' => 'Holder Name',
            'cc_expiration_month' => 'Expiration Month',
            'cc_expiration_year' => 'Expiration Year',
            'cc_cvv' => 'Cvv',
            'cc_type_id' => 'Type ID',
            'cc_status_id' => 'Status ID',
            'cc_is_expired' => 'Is Expired',
            'cc_security_hash' => 'Security Hash',
            'cc_created_user_id' => 'Created User ID',
            'cc_updated_user_id' => 'Updated User ID',
            'cc_created_dt' => 'Created Dt',
            'cc_updated_dt' => 'Updated Dt',
            'cc_bo_link' => 'BO link',
            'cc_is_sync_bo' => 'Sync With B/O',
        ];
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cc_created_dt', 'cc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBillingInfos(): ActiveQuery
    {
        return $this->hasMany(BillingInfo::class, ['bi_cc_id' => 'cc_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCcCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cc_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCcUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'cc_updated_user_id']);
    }

    /**
     * @return CreditCardQuery the active query used by this AR class.
     */
    public static function find(): CreditCardQuery
    {
        return new CreditCardQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getTypeList(): array
    {
        return self::TYPE_LIST;
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return self::TYPE_LIST[$this->cc_type_id] ?? '';
    }


    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->cc_status_id] ?? '';
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return self::STATUS_CLASS_LIST[$this->cc_status_id] ?? '';
    }

    /**
     * @return string
     */
    public function getStatusLabel(): string
    {
        return Html::tag('span', $this->getStatusName(), ['class' => 'badge badge-' . $this->getClassName()]);
    }

    /**
     * @return string
     */
    public function getSecurityKey(): string
    {
        return md5($this->cc_expiration_month . '|' . $this->cc_security_hash . '|' . $this->cc_expiration_year);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function updateSecureCardNumber(): string
    {
        $this->updateSecureHash();
        $this->cc_display_number = CreditCardHelper::maskCreditCard($this->cc_number);
        return $this->cc_number = self::encrypt($this->cc_number, $this->securityKey);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function updateSecureCvv(): string
    {
        $this->updateSecureHash();
        return $this->cc_cvv = self::encrypt($this->cc_cvv, $this->securityKey);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function updateSecureHash(): string
    {
        if (!$this->cc_security_hash) {
            $this->cc_security_hash = md5(\Yii::$app->security->generateRandomString(32));
        }
        return $this->cc_security_hash;
    }

    /**
     * @return string
     */
    public function getInitNumber(): string
    {
        return self::decrypt($this->cc_number, $this->securityKey);
    }

    /**
     * @return string
     */
    public function getInitCvv(): string
    {
        return self::decrypt($this->cc_cvv, $this->securityKey);
    }


    /**
     * @param string $data
     * @param string $key
     * @return string
     */
    public static function encrypt(string $data, string $key = ''): string
    {
        $cryptParams = \Yii::$app->params['crypt'];
        $cryptMethod = (string) $cryptParams['method'] ?? 'aes-256-cbc';
        $cryptPassword = (string) $cryptParams['password'] ?? '';
        $cryptIv = (string) $cryptParams['iv'] ?? '';

        $cryptPassword .= $key;

//        $sql = "SELECT AES_ENCRYPT('".$string."','".$password."', 'DeBijpCtvFCO0bHU') AS aes";
//        $db = \Yii::$app->getDb();

//        $str = $db->createCommand("SET block_encryption_mode = 'aes-256-cbc';")->execute();
//        $str = $db->createCommand($sql)->queryScalar();

        //$encrypted = openssl_encrypt($data, $this->cipher, $key, OPENSSL_RAW_DATA, $iv);
        $strBase64 = openssl_encrypt($data, $cryptMethod, $cryptPassword, 0, $cryptIv);
        return $strBase64 ?: '';
    }

    /**
     * @param string $data
     * @param string $key
     * @return string
     */
    public static function decrypt(string $data, string $key = ''): string
    {
        $cryptParams = \Yii::$app->params['crypt'];
        $cryptMethod = (string) $cryptParams['method'] ?? 'aes-256-cbc';
        $cryptPassword = (string) $cryptParams['password'] ?? '';
        $cryptIv = (string) $cryptParams['iv'] ?? '';

        $cryptPassword .= $key;

        $str = openssl_decrypt($data, $cryptMethod, $cryptPassword, 0, $cryptIv);
        return $str ?: '';
    }

    public static function create(
        ?string $number,
        string $holder,
        int $expirationMonth,
        int $expirationYear,
        ?string $cvv,
        ?int $typeId
    ): CreditCard {
        $card = new self();
        $card->cc_number = $number;
        $card->cc_holder_name = $holder;
        $card->cc_expiration_month = $expirationMonth;
        $card->cc_expiration_year = $expirationYear;
        $card->cc_cvv = $cvv;
        $card->cc_type_id = $typeId;
        return $card;
    }

    public static function getCreditCardByParams(
        int $expirationMonth,
        int $expirationYear,
        ?string $holderName,
        ?int $typeId
    ): ?CreditCard {
        return self::find()
            ->where(['cc_holder_name' => $holderName])
            ->andWhere(['cc_type_id' => $typeId])
            ->andWhere(['cc_expiration_month' => $expirationMonth])
            ->andWhere(['cc_expiration_year' => $expirationYear])
            ->orderBy(['cc_id' => SORT_DESC])
            ->one();
    }
}

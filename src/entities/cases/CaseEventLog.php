<?php

namespace src\entities\cases;

use Yii;
use src\entities\cases\Cases;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "case_event_log".
 *
 * @property int $cel_id
 * @property int|null $cel_case_id
 * @property int|null $cel_type_id
 * @property string|null $cel_description
 * @property string|null $cel_data_json
 * @property string|null $cel_created_dt
 *
 * @property Cases $celCase
 * @property int|null $cel_category_id [tinyint(1)]
 */
class CaseEventLog extends ActiveRecord
{
    public const CASE_CREATED               = 1;
    public const CASE_STATUS_CHANGED        = 2;
    public const CASE_CATEGORY_CHANGE       = 3;
    public const CASE_AUTO_PROCESSING_MARK  = 4;
    public const REPROTECTION_DECISION      = 5;
    public const RE_PROTECTION_CREATE       = 6;
    public const RE_PROTECTION_EXCHANGE     = 7;
    public const VOLUNTARY_EXCHANGE_CREATE  = 8;
    public const VOLUNTARY_EXCHANGE_CONFIRM = 9;
    public const VOLUNTARY_REFUND_CREATE    = 10;
    public const VOLUNTARY_REFUND_WH_UPDATE    = 11;
    public const VOLUNTARY_EXCHANGE_WH_UPDATE  = 12;
    public const VOLUNTARY_REFUND_CONFIRM    = 13;
    public const VOLUNTARY_REFUND_EMAIL_SEND    = 14;
    public const VOLUNTARY_REFUND_WH_SEND_OTA    = 15;
    public const EMAIL_REVIEWED    = 16;
    public const CASE_MARK_CHECKED    = 17;
    public const RE_PROTECTION_REFUND = 18;
    public const VOLUNTARY_PRODUCT_REFUND_ACCEPTED    = 19;
    public const CASE_DEPARTMENT_CHANGE    = 20;
    public const CASE_INFO_UPDATE = 21;
    public const CASE_BOOKINGID_CHANGE = 22;

    public const CASE_EVENT_LOG_LIST = [
        self::CASE_CREATED         => 'Case created',
        self::CASE_STATUS_CHANGED  => 'Case status changed',
        self::CASE_CATEGORY_CHANGE => 'Case category changed',
        self::CASE_DEPARTMENT_CHANGE => 'Case department changed',
        self::CASE_AUTO_PROCESSING_MARK => 'Case auto processing mark has changed',
        self::CASE_MARK_CHECKED => 'Case Mark as Checked',
        self::REPROTECTION_DECISION => 'Reprotection Decision',
        self::RE_PROTECTION_CREATE => 'ReProtection Create',
        self::RE_PROTECTION_EXCHANGE => 'ReProtection Exchange',
        self::RE_PROTECTION_REFUND => 'ReProtection Refund',
        self::VOLUNTARY_EXCHANGE_CREATE => 'Voluntary Exchange Create',
        self::VOLUNTARY_EXCHANGE_CONFIRM => 'Voluntary Exchange Confirm',
        self::VOLUNTARY_REFUND_CREATE => 'Voluntary Refund Create',
        self::VOLUNTARY_REFUND_WH_UPDATE => 'Voluntary Refund Update by Webhook BO',
        self::VOLUNTARY_EXCHANGE_WH_UPDATE => 'Voluntary Exchange Update by WH BO',
        self::VOLUNTARY_REFUND_CONFIRM => 'Voluntary Refund Confirm',
        self::VOLUNTARY_REFUND_EMAIL_SEND => 'Voluntary Refund Send Email to client',
        self::VOLUNTARY_REFUND_WH_SEND_OTA => 'Voluntary Refund Send WH to OTA',
        self::VOLUNTARY_PRODUCT_REFUND_ACCEPTED => 'Voluntary Product Refund Accepted',
        self::EMAIL_REVIEWED => 'Email Reviewed',
        self::CASE_INFO_UPDATE => 'Case info updated',
        self::CASE_BOOKINGID_CHANGE => 'Case bookingId changed',
    ];

    public const CATEGORY_ERROR = 1;
    public const CATEGORY_WARNING = 2;
    public const CATEGORY_INFO = 3;
    public const CATEGORY_DEBUG = 4;

    public const CATEGORY_LIST = [
        self::CATEGORY_ERROR => 'Error',
        self::CATEGORY_WARNING => 'Warning',
        self::CATEGORY_INFO => 'Info',
        self::CATEGORY_DEBUG => 'Debug'
    ];

    public const CATEGORY_ICON_CLASS_LIST = [
        self::CATEGORY_ERROR => 'fas fa-times red',
        self::CATEGORY_WARNING => 'fas fa-exclamation-triangle yellow',
        self::CATEGORY_INFO => 'fas fa-info-circle blue',
        self::CATEGORY_DEBUG => 'fas fa-list'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'case_event_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cel_case_id', 'cel_type_id', 'cel_category_id'], 'integer'],
            [['cel_data_json'], 'safe'],
            ['cel_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['cel_description'], 'string', 'max' => 255],
            [['cel_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['cel_case_id' => 'cs_id']],
            [['cel_category_id'], 'in', 'range' => array_keys(self::getCategoryList()), 'skipOnEmpty' => true]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cel_id' => 'ID',
            'cel_case_id' => 'Case ID',
            'cel_type_id' => 'Type',
            'cel_description' => 'Description',
            'cel_data_json' => 'Data',
            'cel_created_dt' => 'Created Dt',
            'cel_category_id' => 'Category'
        ];
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cel_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Gets query for [[CelCase]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCelCase()
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'cel_case_id']);
    }

    public function beforeSave($insert)
    {
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public static function add(int $caseId, ?int $type, string $description = '', $data = [], ?int $categoryId = null)
    {
        if ($caseId) {
            $log = new self();
            $log->cel_case_id = $caseId;
            $log->cel_type_id = $type;
            $log->cel_description = $description;
            $log->cel_data_json = $data;
            $log->cel_category_id = $categoryId;
            $log->save();
        }
    }

    public static function getEventLogList(): array
    {
        return self::CASE_EVENT_LOG_LIST;
    }

    public static function getCategoryList(): array
    {
        return self::CATEGORY_LIST;
    }

    public static function getCategoryIconClassList(): array
    {
        return self::CATEGORY_ICON_CLASS_LIST;
    }

    public function getCategoryName(): string
    {
        return self::getCategoryList()[$this->cel_category_id] ?? '';
    }

    public function getCategoryNameFormat(): string
    {
        return $this->cel_category_id ? (Html::tag('i', '', ['class' => self::getCategoryIconClassList()[$this->cel_category_id] ?? '']) . ' ' . $this->getCategoryName()) : '';
    }
}

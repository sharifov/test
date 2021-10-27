<?php

namespace sales\entities\cases;

use Yii;
use sales\entities\cases\Cases;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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

    public const CASE_EVENT_LOG_LIST = [
        self::CASE_CREATED         => 'Case created',
        self::CASE_STATUS_CHANGED  => 'Case status changed',
        self::CASE_CATEGORY_CHANGE => 'Case category changed',
        self::CASE_AUTO_PROCESSING_MARK => 'Case auto processing mark has changed',
        self::REPROTECTION_DECISION => 'Reprotection Decision',
        self::RE_PROTECTION_CREATE => 'ReProtection Create',
        self::RE_PROTECTION_EXCHANGE => 'ReProtection Exchange',
        self::VOLUNTARY_EXCHANGE_CREATE => 'Voluntary Exchange Create',
        self::VOLUNTARY_EXCHANGE_CONFIRM => 'Voluntary Exchange Confirm',
        self::VOLUNTARY_REFUND_CREATE => 'Voluntary Refund Create',
        self::VOLUNTARY_REFUND_WH_UPDATE => 'Voluntary Refund Update by Webhook BO',
        self::VOLUNTARY_EXCHANGE_WH_UPDATE => 'Voluntary Exchange Update by WH BO',
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
            [['cel_case_id', 'cel_type_id'], 'integer'],
            [['cel_data_json'], 'safe'],
            ['cel_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['cel_description'], 'string', 'max' => 255],
            [['cel_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['cel_case_id' => 'cs_id']],
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

    public static function add(int $caseId, ?int $type, string $description = '', $data = [])
    {
        if ($caseId) {
            $log = new self();
            $log->cel_case_id = $caseId;
            $log->cel_type_id = $type;
            $log->cel_description = $description;
            $log->cel_data_json = $data;
            $log->save();
        }
    }
}

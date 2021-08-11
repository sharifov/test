<?php

namespace modules\product\src\entities\productQuoteChange;

use sales\entities\cases\Cases;
use modules\product\src\entities\productQuote\ProductQuote;
use common\models\Employee;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "product_quote_change".
 *
 * @property int $pqc_id
 * @property int $pqc_pq_id
 * @property int|null $pqc_case_id
 * @property int|null $pqc_decision_user
 * @property int|null $pqc_status_id
 * @property int|null $pqc_decision_type_id
 * @property string|null $pqc_created_dt
 * @property string|null $pqc_updated_dt
 * @property string|null $pqc_decision_dt
 *
 * @property Cases $pqcCase
 * @property Employee $pqcDecisionUser
 * @property ProductQuote $pqcPq
 */
class ProductQuoteChange extends \yii\db\ActiveRecord
{
    public const STATUS_NEW         = 1;
    public const STATUS_DECISION    = 2;
    public const STATUS_IN_PROGRESS = 3;
    public const STATUS_COMPLETE    = 4;
    public const STATUS_CANCELED    = 5;
    public const STATUS_ERROR       = 6;
    public const STATUS_DECLINED    = 7;
    public const STATUS_DECIDED     = 8;

    public const STATUS_LIST = [
        self::STATUS_NEW         => 'New',
        self::STATUS_DECISION    => 'Decision Pending',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_COMPLETE    => 'Complete',
        self::STATUS_CANCELED    => 'Canceled',
        self::STATUS_ERROR       => 'Error',
        self::STATUS_DECLINED    => 'Declined',
        self::STATUS_DECIDED     => 'Decided',
    ];

    public const DECISION_CONFIRM = 1;
    public const DECISION_MODIFY  = 2;
    public const DECISION_REFUND  = 3;

    public const DECISION_LIST = [
        self::DECISION_CONFIRM => 'Confirm',
        self::DECISION_MODIFY  => 'Modify',
        self::DECISION_REFUND  => 'Refund',
    ];

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pqc_created_dt', 'pqc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pqc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function customerDecisionConfirm(?int $userId, \DateTimeImmutable $date): void
    {
        $this->pqc_decision_user = $userId;
        $this->pqc_status_id = ProductQuoteChangeStatus::DECIDED;
        $this->pqc_decision_type_id = ProductQuoteChangeDecisionType::CONFIRM;
        $this->pqc_decision_dt = $date->format('Y-m-d H:i:s');
    }

    public function isCustomerDecisionConfirm(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::DECIDED && $this->pqc_decision_type_id === ProductQuoteChangeDecisionType::CONFIRM;
    }

    public function isDecisionPending(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::DECISION_PENDING;
    }

    public function inProgress(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::IN_PROGRESS;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_quote_change';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pqc_pq_id'], 'required'],
            [['pqc_pq_id', 'pqc_case_id', 'pqc_decision_user', 'pqc_status_id', 'pqc_decision_type_id'], 'integer'],
            [['pqc_created_dt', 'pqc_updated_dt', 'pqc_decision_dt'], 'safe'],
            [['pqc_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['pqc_case_id' => 'cs_id']],
            [['pqc_decision_user'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqc_decision_user' => 'id']],
            [['pqc_pq_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqc_pq_id' => 'pq_id']],
            ['pqc_status_id', 'in', 'range' => array_keys(self::STATUS_LIST)],
            ['pqc_decision_type_id', 'in', 'range' => array_keys(self::DECISION_LIST)],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pqc_id' => 'ID',
            'pqc_pq_id' => 'Product Quote ID',
            'pqc_case_id' => 'Case ID',
            'pqc_decision_user' => 'Decision User',
            'pqc_status_id' => 'Status ID',
            'pqc_decision_type_id' => 'Decision Type ID',
            'pqc_created_dt' => 'Created Dt',
            'pqc_updated_dt' => 'Updated Dt',
            'pqc_decision_dt' => 'Decision Dt',
        ];
    }

    /**
     * Gets query for [[PqcCase]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPqcCase()
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'pqc_case_id']);
    }

    /**
     * Gets query for [[PqcDecisionUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPqcDecisionUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pqc_decision_user']);
    }

    /**
     * Gets query for [[PqcPq]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPqcPq()
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqc_pq_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}

<?php

namespace modules\product\src\entities\productQuoteChange;

use common\components\validators\CheckAndConvertToJsonValidator;
use common\models\Currency;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeCreatedEvent;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionConfirmEvent;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionModifyEvent;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionRefundEvent;
use modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation;
use src\behaviors\GidBehavior;
use src\entities\cases\Cases;
use modules\product\src\entities\productQuote\ProductQuote;
use common\models\Employee;
use src\entities\EventTrait;
use src\helpers\setting\SettingHelper;
use src\traits\FieldsTrait;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\behaviors\BlameableBehavior;
use src\auth\Auth;

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
 * @property bool $pqc_is_automate [tinyint(1)]
 * @property int|null $pqc_type_id
 * @property array|null $pqc_data_json
 * @property string $pqc_gid
 * @property bool|null $pqc_refund_allowed
 * @property int|null $pqc_created_user_id
 *
 * @property Cases $pqcCase
 * @property Employee $pqcDecisionUser
 * @property Employee $pqcCreatedUser
 * @property ProductQuote $pqcPq
 * @property ProductQuoteChangeRelation[]|null $productQuoteChangeRelations
 * @property-read  ProductQuoteChangeRelation[]|null $newProductQuoteChangeRelations
 * @property-read bool $isNewProductQuote
 */
class ProductQuoteChange extends \yii\db\ActiveRecord
{
    use EventTrait;
    use FieldsTrait;

    public const TYPE_RE_PROTECTION = 1;
    public const TYPE_VOLUNTARY_EXCHANGE = 2;

    public const TYPE_LIST = [
        self::TYPE_RE_PROTECTION => 'Schedule Change',
        self::TYPE_VOLUNTARY_EXCHANGE => 'Voluntary Exchange',
    ];

    public const SHORT_TYPE_LIST = [
        self::TYPE_RE_PROTECTION => 'SC',
        self::TYPE_VOLUNTARY_EXCHANGE => 'Vol',
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
            'generateGid' => [
                'class' => GidBehavior::class,
                'targetColumn' => 'pqc_gid',
                'value' => self::generateGid(),
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'pqc_created_user_id',
                'updatedByAttribute' => null,
                'value' => function () {
                    return Auth::employeeId();
                }
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function customerDecisionConfirm(?int $userId, \DateTimeImmutable $date): void
    {
        $this->pqc_decision_user = $userId;
        $this->pqc_status_id = ProductQuoteChangeStatus::PROCESSING;
        $this->pqc_decision_type_id = ProductQuoteChangeDecisionType::CONFIRM;
        $this->pqc_decision_dt = $date->format('Y-m-d H:i:s');
        $this->recordEvent(new ProductQuoteChangeDecisionConfirmEvent($this->pqc_id, $this->pqc_pq_id));
    }

    public function customerDecisionRefund(?int $userId, \DateTimeImmutable $date): void
    {
        $this->pqc_decision_user = $userId;
        $this->pqc_status_id = ProductQuoteChangeStatus::PROCESSING;
        $this->pqc_decision_type_id = ProductQuoteChangeDecisionType::REFUND;
        $this->pqc_decision_dt = $date->format('Y-m-d H:i:s');
        $this->recordEvent(new ProductQuoteChangeDecisionRefundEvent($this->pqc_id, $this->pqc_pq_id));
    }

    public function customerDecisionModify(?int $userId, \DateTimeImmutable $date): void
    {
        $this->pqc_decision_user = $userId;
        $this->pqc_status_id = ProductQuoteChangeStatus::PROCESSING;
        $this->pqc_decision_type_id = ProductQuoteChangeDecisionType::MODIFY;
        $this->pqc_decision_dt = $date->format('Y-m-d H:i:s');
        $this->recordEvent(new ProductQuoteChangeDecisionModifyEvent($this->pqc_id, $this->pqc_pq_id));
    }

    public function isCustomerDecisionConfirm(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::PROCESSING && $this->pqc_decision_type_id === ProductQuoteChangeDecisionType::CONFIRM;
    }

    public function isCustomerDecisionModify(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::PROCESSING && $this->pqc_decision_type_id === ProductQuoteChangeDecisionType::MODIFY;
    }

    public function isCustomerDecisionRefund(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::PROCESSING && $this->pqc_decision_type_id === ProductQuoteChangeDecisionType::REFUND;
    }

    public function decisionToCreate(): ProductQuoteChange
    {
        $this->pqc_decision_type_id =  ProductQuoteChangeDecisionType::CREATE;
        $this->pqc_decision_dt = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        return $this;
    }

    public function decisionToConfirm(): ProductQuoteChange
    {
        $this->pqc_decision_type_id =  ProductQuoteChangeDecisionType::CONFIRM;
        $this->pqc_decision_dt = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        return $this;
    }

    public function isPending(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::PENDING;
    }

    public function isStatusNew(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::NEW;
    }

    public function isDeclined(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::DECLINED;
    }

    public function statusToNew(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::NEW;
    }

    public function inProgress(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::IN_PROGRESS;
    }

    public function cancel(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::CANCELED;
    }

    public function declined(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::DECLINED;
    }

    public function statusToPending(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::PENDING;
    }

    public function error(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::ERROR;
    }

    public function statusToComplete(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::COMPLETED;
    }

    public function statusToConfirmed(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::CONFIRMED;
    }

    public function statusToProcessing(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::PROCESSING;
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
            [['pqc_is_automate'], 'boolean'],
            [['pqc_case_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cases::class, 'targetAttribute' => ['pqc_case_id' => 'cs_id']],
            [['pqc_decision_user'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqc_decision_user' => 'id']],
            [['pqc_pq_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqc_pq_id' => 'pq_id']],
            ['pqc_status_id', 'in', 'range' => array_keys(ProductQuoteChangeStatus::LIST)],
            ['pqc_decision_type_id', 'in', 'range' => array_keys(ProductQuoteChangeDecisionType::LIST)],

            ['pqc_type_id', 'integer'],
            ['pqc_type_id', 'in', 'range' => array_keys(self::TYPE_LIST)],
            ['pqc_type_id', 'default', 'value' => self::TYPE_RE_PROTECTION],

            ['pqc_data_json', CheckAndConvertToJsonValidator::class, 'skipOnEmpty' => true],

            ['pqc_gid', 'string', 'max' => 32],

            [['pqc_refund_allowed'], 'boolean'],
            [['pqc_refund_allowed'], 'default', 'value' => true],

            ['pqc_created_user_id', 'integer'],
            ['pqc_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqc_created_user_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pqc_id' => 'ID',
            'pqc_pq_id' => 'Origin Product Quote ID',
            'pqc_case_id' => 'Case ID',
            'pqc_decision_user' => 'Decision User',
            'pqc_status_id' => 'Status',
            'pqc_decision_type_id' => 'Decision Type',
            'pqc_created_dt' => 'Created Dt',
            'pqc_updated_dt' => 'Updated Dt',
            'pqc_decision_dt' => 'Decision Dt',
            'pqc_is_automate' => 'Is Automate',
            'pqc_type_id' => 'Type ID',
            'pqc_data_json' => 'Data Json',
            'pqc_gid' => 'Gid',
            'pqc_refund_allowed' => 'Refund allowed',
            'pqc_created_user_id' => 'Created User',
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
     * @return \yii\db\ActiveQuery
     */
    public function getPqcCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'pqc_created_user_id']);
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

    public function getProductQuoteChangeRelations(): \yii\db\ActiveQuery
    {
        return $this->hasMany(ProductQuoteChangeRelation::class, ['pqcr_pqc_id' => 'pqc_id']);
    }

    /**
     * Returns Quote change relations with quote data in status NEW
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewProductQuoteChangeRelations(): \yii\db\ActiveQuery
    {
         return $this->getProductQuoteChangeRelations()->innerJoinWith(['newProductQuote']);
    }

    /**
     * Returns true if a product quote in status NEW exists
     *
     * @return bool
     */
    public function getIsNewProductQuote(): bool
    {
        return $this->getNewProductQuoteChangeRelations()->exists();
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    private static function createNew(
        int $productQuoteId,
        ?int $caseId,
        ?bool $isAutomate,
        ?bool $refundAllowed = null
    ): ProductQuoteChange {
        $model = new self();
        $model->pqc_pq_id = $productQuoteId;
        $model->pqc_case_id = $caseId;
        $model->pqc_status_id = ProductQuoteChangeStatus::NEW;
        $model->pqc_is_automate = $isAutomate;
        $model->pqc_refund_allowed = $refundAllowed;
        $model->recordEvent(new ProductQuoteChangeCreatedEvent($model, $model->pqc_pq_id, $model->pqc_case_id));
        return $model;
    }

    public static function createReProtection(
        int $productQuoteId,
        ?int $caseId,
        bool $isAutomate,
        bool $refundAllowed = true
    ): ProductQuoteChange {
        $model = self::createNew($productQuoteId, $caseId, $isAutomate, $refundAllowed);
        $model->pqc_type_id = self::TYPE_RE_PROTECTION;
        return $model;
    }

    public static function createVoluntaryExchange(
        int $productQuoteId,
        ?int $caseId,
        ?bool $isAutomate = null
    ): ProductQuoteChange {
        $model = self::createNew($productQuoteId, $caseId, $isAutomate);
        $model->pqc_type_id = self::TYPE_VOLUNTARY_EXCHANGE;
        return $model;
    }

    public function setDataJson(array $data): ProductQuoteChange
    {
        $this->pqc_data_json = $data;
        return $this;
    }

    public function onIsAutomate(): ProductQuoteChange
    {
        $this->pqc_is_automate = true;
        return $this;
    }

    public function offIsAutomate(): ProductQuoteChange
    {
        $this->pqc_is_automate = false;
        return $this;
    }

    public function isAutomate(): bool
    {
        return (bool)$this->pqc_is_automate;
    }

    public function isActiveStatus(): bool
    {
        return in_array($this->pqc_status_id, SettingHelper::getActiveQuoteChangeStatuses(), false);
    }

    public function isFinishedStatus(): bool
    {
        return in_array($this->pqc_status_id, SettingHelper::getFinishedQuoteChangeStatuses(), false);
    }

    /**
     * @return string
     */
    public function getShortTypeName(): string
    {
        return self::SHORT_TYPE_LIST[$this->pqc_type_id] ?? '-';
    }

    /**
     * @return string
     */
    public function getTypeName(): string
    {
        return self::TYPE_LIST[$this->pqc_type_id] ?? '-';
    }

    /**
     * @return string
     */
    public function getStatusLabel(): string
    {
        return $this->pqc_status_id ? ProductQuoteChangeStatus::asFormat($this->pqc_status_id) : '-';
    }

    /**
     * @return string
     */
    public function getDecisionTypeLabel(): string
    {
        return $this->pqc_decision_type_id ? ProductQuoteChangeDecisionType::asFormat($this->pqc_decision_type_id) : '-';
    }

    public function isTypeReProtection(): string
    {
        return (int) $this->pqc_type_id === self::TYPE_RE_PROTECTION;
    }

    public function isTypeVoluntary(): bool
    {
        return (int) $this->pqc_type_id === self::TYPE_VOLUNTARY_EXCHANGE;
    }

    public static function generateGid(): string
    {
        return md5(uniqid('pqc', false));
    }

    public function getClientStatusName(): string
    {
        return $this->pqc_status_id ? ProductQuoteChangeStatus::getClientKeyStatusById($this->pqc_status_id) : '-';
    }

    public function getSystemStatusName(): string
    {
        return $this->pqc_status_id ? ProductQuoteChangeStatus::getName($this->pqc_status_id) : '-';
    }
}

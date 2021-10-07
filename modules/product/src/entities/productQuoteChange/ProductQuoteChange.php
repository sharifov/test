<?php

namespace modules\product\src\entities\productQuoteChange;

use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeCreatedEvent;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionConfirmEvent;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionModifyEvent;
use modules\product\src\entities\productQuoteChange\events\ProductQuoteChangeDecisionRefundEvent;
use sales\entities\cases\Cases;
use modules\product\src\entities\productQuote\ProductQuote;
use common\models\Employee;
use sales\entities\EventTrait;
use sales\traits\FieldsTrait;
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
 * @property bool $pqc_is_automate [tinyint(1)]
 *
 * @property Cases $pqcCase
 * @property Employee $pqcDecisionUser
 * @property ProductQuote $pqcPq
 */
class ProductQuoteChange extends \yii\db\ActiveRecord
{
    use EventTrait;
    use FieldsTrait;

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
        $this->recordEvent(new ProductQuoteChangeDecisionConfirmEvent($this->pqc_id, $this->pqc_pq_id));
    }

    public function customerDecisionRefund(?int $userId, \DateTimeImmutable $date): void
    {
        $this->pqc_decision_user = $userId;
        $this->pqc_status_id = ProductQuoteChangeStatus::DECIDED;
        $this->pqc_decision_type_id = ProductQuoteChangeDecisionType::REFUND;
        $this->pqc_decision_dt = $date->format('Y-m-d H:i:s');
        $this->recordEvent(new ProductQuoteChangeDecisionRefundEvent($this->pqc_id, $this->pqc_pq_id));
    }

    public function customerDecisionModify(?int $userId, \DateTimeImmutable $date): void
    {
        $this->pqc_decision_user = $userId;
        $this->pqc_status_id = ProductQuoteChangeStatus::DECIDED;
        $this->pqc_decision_type_id = ProductQuoteChangeDecisionType::MODIFY;
        $this->pqc_decision_dt = $date->format('Y-m-d H:i:s');
        $this->recordEvent(new ProductQuoteChangeDecisionModifyEvent($this->pqc_id, $this->pqc_pq_id));
    }

    public function isCustomerDecisionConfirm(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::DECIDED && $this->pqc_decision_type_id === ProductQuoteChangeDecisionType::CONFIRM;
    }

    public function isCustomerDecisionModify(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::DECIDED && $this->pqc_decision_type_id === ProductQuoteChangeDecisionType::MODIFY;
    }

    public function isCustomerDecisionRefund(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::DECIDED && $this->pqc_decision_type_id === ProductQuoteChangeDecisionType::REFUND;
    }

    public function isDecisionPending(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::DECISION_PENDING;
    }

    public function isStatusNew(): bool
    {
        return $this->pqc_status_id === ProductQuoteChangeStatus::NEW;
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

    public function decisionPending(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::DECISION_PENDING;
    }

    public function error(): void
    {
        $this->pqc_status_id = ProductQuoteChangeStatus::ERROR;
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
            'pqc_status_id' => 'Status',
            'pqc_decision_type_id' => 'Decision Type',
            'pqc_created_dt' => 'Created Dt',
            'pqc_updated_dt' => 'Updated Dt',
            'pqc_decision_dt' => 'Decision Dt',
            'pqc_is_automate' => 'Is Automate'
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

    public static function createNew(int $productQuoteId, ?int $caseId, bool $isAutomate): ProductQuoteChange
    {
        $model = new self();
        $model->pqc_pq_id = $productQuoteId;
        $model->pqc_case_id = $caseId;
        $model->pqc_status_id = ProductQuoteChangeStatus::NEW;
        $model->pqc_is_automate = $isAutomate;
        $model->recordEvent(new ProductQuoteChangeCreatedEvent($model, $model->pqc_pq_id, $model->pqc_case_id));
        return $model;
    }
}

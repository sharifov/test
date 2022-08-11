<?php

namespace modules\order\src\entities\order;

use common\models\BillingInfo;
use common\models\Client;
use common\models\Currency;
use common\models\Employee;
use common\models\Payment;
use common\models\Project;
use modules\invoice\src\entities\invoice\Invoice;
use common\models\Lead;
use modules\order\src\entities\order\events\OrderCanceledEvent;
use modules\order\src\entities\order\events\OrderCancelFailedEvent;
use modules\order\src\entities\order\events\OrderCancelProcessingEvent;
use modules\order\src\entities\order\events\OrderCompletedEvent;
use modules\order\src\entities\order\events\OrderDeclinedEvent;
use modules\order\src\entities\order\events\OrderErrorEvent;
use modules\order\src\entities\order\events\OrderNewEvent;
use modules\order\src\entities\order\events\OrderPaymentPaidEvent;
use modules\order\src\entities\order\events\OrderPendingEvent;
use modules\order\src\entities\order\events\OrderPreparedEvent;
use modules\order\src\entities\order\events\OrderUserProfitUpdateProfitAmountEvent;
use modules\order\src\entities\order\serializer\OrderSerializer;
use modules\order\src\entities\orderContact\OrderContact;
use modules\order\src\entities\orderRequest\OrderRequest;
use modules\order\src\entities\orderTips\OrderTips;
use modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\services\CreateOrderDTO;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\interfaces\ProductDataInterface;
use src\entities\EventTrait;
use src\entities\serializer\Serializable;
use src\helpers\product\ProductQuoteHelper;
use src\model\caseOrder\entity\CaseOrder;
use src\model\leadOrder\entity\LeadOrder;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use modules\order\src\entities\orderStatusLog\OrderStatusLog;
use modules\order\src\entities\orderData\OrderData;

/**
 * This is the model class for table "order".
 *
 * @property int $or_id
 * @property string $or_gid
 * @property string|null $or_uid
 * @property string $or_fare_id
 * @property string|null $or_name
 * @property int|null $or_lead_id
 * @property string|null $or_description
 * @property int|null $or_status_id
 * @property int|null $or_pay_status_id
 * @property float|null $or_app_total
 * @property float|null $or_app_markup
 * @property float|null $or_agent_markup
 * @property float|null $or_client_total
 * @property string|null $or_client_currency
 * @property float|null $or_client_currency_rate
 * @property int|null $or_owner_user_id
 * @property int|null $or_created_user_id
 * @property int|null $or_updated_user_id
 * @property string|null $or_created_dt
 * @property string|null $or_updated_dt
 * @property float|null $or_profit_amount
 * @property array|null $or_request_data
 * @property int $or_request_id [int]
 * @property int $or_project_id [int]
 * @property int $or_type_id [tinyint(1)]
 * @property int $or_sale_id
 *
 * @property Currency $orClientCurrency
 * @property Invoice[] $invoices
 * @property Lead $orLead
 * @property Employee $orCreatedUser
 * @property Employee $orOwnerUser
 * @property Employee $orUpdatedUser
 * @property ProductQuote[] $productQuotesActive
 * @property float $orderTotalCalcSum
 * @property ProductQuote[] $productQuotes
 * @property OrderUserProfit[] $orderUserProfit
 * @property OrderTips $orderTips
 * @property OrderTipsUserProfit[] $orderTipsUserProfit
 * @property BillingInfo[] $billingInfo
 * @property Payment[] $payments
 * @property OrderRequest $orderRequest
 * @property Project $relatedProject
 * @property ProductQuote[] $productQuotesApplied
 * @property OrderContact[] $orderContacts
 * @property Project $project
 * @property LeadOrder[] $leadOrder
 * @property CaseOrder[] $caseOrder
 * @property OrderStatusLog[] $orderStatusLogs
 * @property OrderData $orderData
 * @property ProductQuote[] $nonReprotectionProductQuotes
 */
class Order extends ActiveRecord implements Serializable, ProductDataInterface
{
    use EventTrait;

    public const UPDATE_EVENT_KEY = 'orderUpdateEvent';

    public static function tableName(): string
    {
        return 'order';
    }

    public function rules(): array
    {
        return [
            [['or_gid'], 'required'],
            [['or_lead_id', 'or_status_id', 'or_pay_status_id', 'or_owner_user_id', 'or_created_user_id', 'or_updated_user_id'], 'integer'],
            [['or_description'], 'string'],
            [['or_app_total', 'or_app_markup', 'or_agent_markup', 'or_client_total', 'or_client_currency_rate', 'or_profit_amount'], 'number'],
            [['or_created_dt', 'or_updated_dt'], 'safe'],
            [['or_gid'], 'string', 'max' => 32],
            [['or_fare_id'], 'string', 'max' => 255],
            [['or_uid'], 'string', 'max' => 15],
            [['or_name'], 'string', 'max' => 40],
            [['or_client_currency'], 'string', 'max' => 3],
            [['or_client_currency'], 'default', 'value' => null],
            [['or_gid'], 'unique'],
            [['or_uid'], 'unique'],
            [['or_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['or_client_currency' => 'cur_code']],
            [['or_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['or_lead_id' => 'id']],
            [['or_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['or_created_user_id' => 'id']],
            [['or_owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['or_owner_user_id' => 'id']],
            [['or_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['or_updated_user_id' => 'id']],

            [['or_request_id', 'or_project_id', 'or_type_id'], 'integer'],

            [['or_request_id'], 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => OrderRequest::class, 'targetAttribute' => ['or_request_id' => 'orr_id']],
            [['or_project_id'], 'exist', 'skipOnError' => true, 'skipOnEmpty' => true, 'targetClass' => Project::class, 'targetAttribute' => ['or_project_id' => 'id']],
            [['or_type_id'], 'in', 'range' => array_keys(OrderSourceType::LIST), 'skipOnEmpty' => true],

            ['or_request_data', 'safe'],

            [['or_sale_id'], 'integer'],
            [['or_sale_id'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'or_id' => 'ID',
            'or_gid' => 'GID',
            'or_uid' => 'UID',
            'or_name' => 'Name',
            'or_lead_id' => 'Lead ID',
            'orLead' => 'Lead',
            'or_description' => 'Description',
            'or_status_id' => 'Status',
            'or_pay_status_id' => 'Pay Status',
            'or_app_total' => 'App Total',
            'or_app_markup' => 'App Markup',
            'or_agent_markup' => 'Agent Markup',
            'or_client_total' => 'Client Total',
            'or_client_currency' => 'Client Currency',
            'or_client_currency_rate' => 'Client Currency Rate',
            'or_owner_user_id' => 'Owner',
            'or_created_user_id' => 'Created User',
            'or_updated_user_id' => 'Updated User',
            'or_created_dt' => 'Created Dt',
            'or_updated_dt' => 'Updated Dt',
            'or_profit_amount' => 'Profit amount',
            'or_request_data' => 'Request Data',
            'or_project_id' => 'Project',
            'or_fare_id' => 'Fare Id',
            'or_type_id' => 'Source Type',
            'or_sale_id' => 'Sale Id',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['or_created_dt', 'or_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['or_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
//            'user' => [
//                'class' => BlameableBehavior::class,
//                'createdByAttribute' => 'or_created_user_id',
//                'updatedByAttribute' => 'or_updated_user_id',
//            ],
        ];
    }

    public function create(CreateOrderDTO $dto): self
    {
        $this->or_gid = self::generateGid();
        $this->or_uid = self::generateUid();
        $this->or_fare_id = $dto->fareId;
        $this->or_status_id = $dto->status;
        $this->or_pay_status_id = $dto->payStatus;
        $this->or_lead_id = $dto->leadId;
        $this->or_name = $this->generateName((int)$dto->leadId);
        $this->or_client_currency = $dto->clientCurrency;
        if ($this->orLead && $this->orLead->employee_id) {
            $this->or_owner_user_id = $this->orLead->employee_id;
        }
//        if (!$this->or_name && $this->or_lead_id) {
//            $this->or_name = $this->generateName($dto->leadId);
//        }

        $this->or_request_data = $dto->requestData;

        $this->or_request_id = $dto->requestId;
        $this->or_project_id = $dto->projectId;
        $this->or_type_id = $dto->creationTypeId;
        $this->or_sale_id = $dto->saleId;

        return $this;
    }

    public function calculateTotalPrice(): void
    {
        $this->or_app_total = $this->orderTotalCalcSum;
        $this->updateOrderTotalByCurrency();
    }

    /**
     * @return ActiveQuery
     */
    public function getOrClientCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'or_client_currency']);
    }

    /**
     * @return ActiveQuery
     */
    public function getInvoices(): ActiveQuery
    {
        return $this->hasMany(Invoice::class, ['inv_order_id' => 'or_id']);
    }

    public function getPayments(): ActiveQuery
    {
        return $this->hasMany(Payment::class, ['pay_order_id' => 'or_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'or_lead_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'or_created_user_id']);
    }

    public function getLeadOrder(): ActiveQuery
    {
        return $this->hasMany(LeadOrder::class, ['lo_order_id' => 'or_id']);
    }

    public function getCaseOrder(): ActiveQuery
    {
        return $this->hasMany(CaseOrder::class, ['co_order_id' => 'or_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderRequest(): ActiveQuery
    {
        return $this->hasOne(OrderRequest::class, ['orr_id' => 'or_request_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRelatedProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'or_project_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderContacts(): ActiveQuery
    {
        return $this->hasMany(OrderContact::class, ['oc_order_id' => 'or_id'])->orderBy(['oc_id' => SORT_DESC]);
    }

    public function getOrderData()
    {
        return $this->hasOne(OrderData::class, ['od_order_id' => 'or_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrOwnerUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'or_owner_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'or_updated_user_id']);
    }

    public function getOrderUserProfit(): ActiveQuery
    {
        return $this->hasMany(OrderUserProfit::class, ['oup_order_id' => 'or_id']);
    }

    public function getBillingInfo(): ActiveQuery
    {
        return $this->hasMany(BillingInfo::class, ['bi_order_id' => 'or_id']);
    }

    public function getOrderStatusLogs(): ActiveQuery
    {
        return $this->hasMany(OrderStatusLog::className(), ['orsl_order_id' => 'or_id']);
    }

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProductQuotesActive(): ActiveQuery
    {
        return $this->hasMany(ProductQuote::class, ['pq_order_id' => 'or_id'])
            ->where(['not', ['pq_status_id' => ProductQuoteStatus::CANCEL_GROUP]]);
    }

    public function getProductQuotesApplied(): ActiveQuery
    {
        return $this->hasMany(ProductQuote::class, ['pq_order_id' => 'or_id'])
            ->where(['pq_status_id' => ProductQuoteStatus::APPLIED]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductQuotes(): ActiveQuery
    {
        return $this->hasMany(ProductQuote::class, ['pq_order_id' => 'or_id']);
    }

    public function getNonReprotectionProductQuotes(): ActiveQuery
    {
        return $this->hasMany(ProductQuote::class, ['pq_order_id' => 'or_id'])
            ->leftJoin([
                'quote_relation' => ProductQuoteRelation::find()
                    ->select([
                        'pqr_parent_pq_id AS parent_id'
                    ])
                    ->groupBy(['pqr_parent_pq_id'])
            ], 'pq_id = quote_relation.parent_id')
            ->leftJoin([
                'quote_non_relation' => ProductQuoteRelation::find()
                    ->select([
                        'pqr_parent_pq_id AS parent_id',
                        'pqr_related_pq_id AS related_id',
                    ])
                    ->groupBy(['pqr_parent_pq_id', 'pqr_related_pq_id'])
            ], 'quote_non_relation.parent_id = pq_id OR quote_non_relation.related_id = pq_id')
            ->andWhere([
            'OR',
                ['IS NOT', 'quote_relation.parent_id', null],
                ['IS', 'quote_non_relation.parent_id', null],
                [
                    'AND',
                    ['IS NOT', 'quote_non_relation.parent_id', null],
                    [
                        'OR',
                        ['=', 'product_quote.pq_status_id', ProductQuoteStatus::BOOKED],
//                        ['=', 'product_quote.pq_status_id', ProductQuoteStatus::DECLINED]
                    ]
                ]
            ])
#            ->createCommand()->getRawSql(); die;
        ;
    }

    public function getOrderTips(): ActiveQuery
    {
        return $this->hasOne(OrderTips::class, ['ot_order_id' => 'or_id']);
    }

    public function getOrderTipsUserProfit(): ActiveQuery
    {
        return $this->hasMany(OrderTipsUserProfit::class, ['otup_order_id' => 'or_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    /**
     * @return string
     */
    public static function generateGid(): string
    {
        return md5(uniqid('or', true));
    }

    /**
     * @return string
     */
    public static function generateUid(): string
    {
        return uniqid('or');
    }

    /**
     * @return string
     */
    public function generateName(int $leadId): string
    {
        $count = self::find()->joinLeadOrdersByLead($leadId)->count();
        return 'Order ' . ($count + 1);
    }

    /**
     * @return float
     */
    public function getOrderTotalCalcSum(): float
    {
        $sum = 0;
        $quotes = $this->productQuotesActive;
        if ($quotes) {
            foreach ($quotes as $quote) {
                $sum += $quote->totalCalcSum;
            }
            $sum = round($sum, 2);
        }
        return $sum;
    }

    public function updateOrderTotalByCurrency(): void
    {
        if ($this->orClientCurrency) {
            $this->or_client_currency_rate = (float) $this->orClientCurrency->cur_app_rate;
        }

        if ($this->or_app_total && $this->or_client_currency_rate) {
            $this->or_client_total = round($this->or_app_total * $this->or_client_currency_rate, 2);
        }
    }

    /**
     * @return float
     * @throws \yii\base\InvalidConfigException
     */
    public function profitCalc(): float
    {
        $sum = 0;
        if ($productQuotes = $this->productQuotesActive) {
            foreach ($productQuotes as $productQuote) {
                /** @var ProductQuote $productQuote */
                $sum += $productQuote->pq_profit_amount;
            }
        }
        return $sum;
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function recalculateProfitAmount(): bool
    {
        $changed = false;
        $profitNew = ProductQuoteHelper::roundPrice($this->profitCalc());
        $profitOld = ProductQuoteHelper::roundPrice((float) $this->or_profit_amount);

        if ($profitNew !== $profitOld) {
            $this->or_profit_amount = $profitNew;
            $changed = true;
            $this->recordEvent((new OrderUserProfitUpdateProfitAmountEvent($this)));
        }
        return $changed;
    }

    public function isProcessing(): bool
    {
        return $this->or_status_id === OrderStatus::PROCESSING;
    }

    public function processing(?string $description, ?int $actionId, ?int $creatorId): void
    {
        $startStatus = $this->or_status_id;
        $this->or_status_id = OrderStatus::PROCESSING;
        $this->recordEvent(
            new OrderProcessingEvent(
                $this,
                $startStatus,
                $this->or_status_id,
                $description,
                $actionId,
                $this->or_owner_user_id,
                $creatorId
            )
        );
//        if (!$this->isProcessing()) {
//            OrderStatus::guard($this->or_status_id, OrderStatus::PROCESSING);
//            foreach ($this->productQuotes as $productQuote) {
//                if (OrderStatus::guardOrder(OrderStatus::PROCESSING, $productQuote->pq_status_id)) {
//                    $this->setStatus(OrderStatus::PROCESSING);
//                    break;
//                }
//            }
//        }
    }

    private function setStatus(int $status): void
    {
        if (!array_key_exists($status, OrderStatus::getList())) {
            throw new \InvalidArgumentException('Invalid Status');
        }
//        OrderStatus::guard($this->or_status_id, $status);

        $this->or_status_id = $status;
    }

    public function prepare(?string $description, ?int $actionId, ?int $creatorId): void
    {
        $startStatus = $this->or_status_id;
        $this->setStatus(OrderStatus::PREPARED);
        $this->recordEvent(
            new OrderPreparedEvent(
                $this->or_id,
                $startStatus,
                $this->or_status_id,
                $description,
                $actionId,
                $this->or_owner_user_id,
                $creatorId
            )
        );
    }

    public function paymentPaid(\DateTimeImmutable $date): void
    {
        if ($this->isPaymentPaid()) {
            throw new \DomainException('Order payment is already paid. Id: ' . $this->or_id);
        }
        $this->or_pay_status_id = OrderPayStatus::PAID;
        $this->recordEvent(new OrderPaymentPaidEvent($this->or_id, $date->format('Y-m-d H:i:s.u')));
    }

    public function isPaymentPaid(): bool
    {
        return $this->or_pay_status_id === OrderPayStatus::PAID;
    }

    public function complete(?string $description, ?int $actionId, ?int $creatorId): void
    {
        if ($this->isComplete()) {
            throw new \DomainException('Order is already complete.');
        }
        $startStatus = $this->or_status_id;
        $this->setStatus(OrderStatus::COMPLETE);
        $this->recordEvent(
            new OrderCompletedEvent(
                $this->or_id,
                $startStatus,
                $this->or_status_id,
                $description,
                $actionId,
                $this->or_owner_user_id,
                $creatorId
            )
        );
    }

    public function isComplete(): bool
    {
        return $this->or_status_id === OrderStatus::COMPLETE;
    }

    public function cancel(?string $description, ?int $actionId, ?int $creatorId): void
    {
        if ($this->isCanceled()) {
            throw new \DomainException('Order is already canceled.');
        }
        $startStatus = $this->or_status_id;
        $this->setStatus(OrderStatus::CANCELED);
        $this->recordEvent(
            new OrderCanceledEvent(
                $this->or_id,
                $startStatus,
                $this->or_status_id,
                $description,
                $actionId,
                $this->or_owner_user_id,
                $creatorId
            )
        );
    }

    public function cancelFailed(?string $description, ?int $actionId, ?int $creatorId): void
    {
        if ($this->isCancelFailed()) {
            throw new \DomainException('Order is already cancel failed.');
        }
        $startStatus = $this->or_status_id;
        $this->setStatus(OrderStatus::CANCEL_FAILED);
        $this->recordEvent(
            new OrderCancelFailedEvent(
                $this->or_id,
                $startStatus,
                $this->or_status_id,
                $description,
                $actionId,
                $this->or_owner_user_id,
                $creatorId
            )
        );
    }

    public function isCancelFailed(): bool
    {
        return $this->or_status_id === OrderStatus::CANCEL_FAILED;
    }

    public function isCanceled(): bool
    {
        return $this->or_status_id === OrderStatus::CANCELED;
    }

    public function new(?string $description, ?int $actionId, ?int $creatorId): void
    {
        $startStatus = $this->or_status_id;
        $this->setStatus(OrderStatus::NEW);
        $this->recordEvent(
            new OrderNewEvent(
                $this->or_id,
                $startStatus,
                $this->or_status_id,
                $description,
                $actionId,
                $this->or_owner_user_id,
                $creatorId
            )
        );
    }

    public function pending(?string $description, ?int $actionId, ?int $creatorId): void
    {
        $startStatus = $this->or_status_id;
        $this->setStatus(OrderStatus::PENDING);
        $this->recordEvent(
            new OrderPendingEvent(
                $this->or_id,
                $startStatus,
                $this->or_status_id,
                $description,
                $actionId,
                $this->or_owner_user_id,
                $creatorId
            )
        );
    }

    public function cancelProcessing(?string $description, ?int $actionId, ?int $creatorId): void
    {
        $startStatus = $this->or_status_id;
        $this->setStatus(OrderStatus::CANCEL_PROCESSING);
        $this->recordEvent(
            new OrderCancelProcessingEvent(
                $this->or_id,
                $startStatus,
                $this->or_status_id,
                $description,
                $actionId,
                $this->or_owner_user_id,
                $creatorId
            )
        );
    }

    public function isCancelProcessing(): bool
    {
        return $this->or_status_id === OrderStatus::CANCEL_PROCESSING;
    }

    public function error(?string $description, ?int $actionId, ?int $creatorId): void
    {
        $startStatus = $this->or_status_id;
        $this->setStatus(OrderStatus::ERROR);
        $this->recordEvent(
            new OrderErrorEvent(
                $this->or_id,
                $startStatus,
                $this->or_status_id,
                $description,
                $actionId,
                $this->or_owner_user_id,
                $creatorId
            )
        );
    }

    public function decline(?string $description, ?int $actionId, ?int $creatorId): void
    {
        $startStatus = $this->or_status_id;
        $this->setStatus(OrderStatus::DECLINED);
        $this->recordEvent(
            new OrderDeclinedEvent(
                $this->or_id,
                $startStatus,
                $this->or_status_id,
                $description,
                $actionId,
                $this->or_owner_user_id,
                $creatorId
            )
        );
    }

    public function isDeclined(): bool
    {
        return $this->or_status_id === OrderStatus::DECLINED;
    }

    public function serialize(): array
    {
        return (new OrderSerializer($this))->getData();
    }

    public function getProject(): ?Project
    {
        if ($this->relatedProject) {
            return $this->relatedProject;
        }
        if ($this->getLead()) {
            return $this->getLead()->project;
        }
        throw new \DomainException('Order not related to project');
    }

    public function getLead(): ?Lead
    {
        return $this->orLead;
    }

    public function getClient(): ?Client
    {
        return $this->getLead() ? $this->orLead->client : null;
    }

    public function getOrder(): Order
    {
        return $this;
    }

    public function getId(): int
    {
        return $this->or_id;
    }

    public function getOrderTipsAmount(): float
    {
        return $this->orderTips->ot_amount ?? 0.00;
    }

    public function isClickToBook(): bool
    {
        return $this->or_type_id === OrderSourceType::C2B;
    }

    public function isPhoneToBook(): bool
    {
        return $this->or_type_id === OrderSourceType::P2B;
    }

    public function getOrderSourceType(): string
    {
        return OrderSourceType::LIST[$this->or_type_id] ?? 'Unknown';
    }

    /**
     * @param int|null $userId
     * @return bool
     */
    public function isOwner(?int $userId): bool
    {
        if ($userId === null) {
            return false;
        }
        return $this->or_owner_user_id === $userId;
    }
}

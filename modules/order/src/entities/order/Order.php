<?php

namespace modules\order\src\entities\order;

use common\models\Currency;
use common\models\Employee;
use modules\invoice\src\entities\invoice\Invoice;
use common\models\Lead;
use modules\order\src\entities\order\events\OrderRecalculateProfitAmountEvent;
use modules\order\src\entities\order\events\OrderUserProfitUpdateProfitAmountEvent;
use modules\order\src\entities\orderUserProfit\OrderUserProfit;
use modules\order\src\services\CreateOrderDTO;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use sales\entities\EventTrait;
use sales\helpers\product\ProductQuoteHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "order".
 *
 * @property int $or_id
 * @property string $or_gid
 * @property string|null $or_uid
 * @property string|null $or_name
 * @property int $or_lead_id
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
 */
class Order extends ActiveRecord
{
    use EventTrait;

    public static function tableName(): string
    {
        return 'order';
    }

    public function rules(): array
    {
        return [
            [['or_gid', 'or_lead_id'], 'required'],
            [['or_lead_id', 'or_status_id', 'or_pay_status_id', 'or_owner_user_id', 'or_created_user_id', 'or_updated_user_id'], 'integer'],
            [['or_description'], 'string'],
            [['or_app_total', 'or_app_markup', 'or_agent_markup', 'or_client_total', 'or_client_currency_rate', 'or_profit_amount'], 'number'],
            [['or_created_dt', 'or_updated_dt'], 'safe'],
            [['or_gid'], 'string', 'max' => 32],
            [['or_uid'], 'string', 'max' => 15],
            [['or_name'], 'string', 'max' => 40],
            [['or_client_currency'], 'string', 'max' => 3],
            [['or_gid'], 'unique'],
            [['or_uid'], 'unique'],
            [['or_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['or_client_currency' => 'cur_code']],
            [['or_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['or_lead_id' => 'id']],
            [['or_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['or_created_user_id' => 'id']],
            [['or_owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['or_owner_user_id' => 'id']],
            [['or_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['or_updated_user_id' => 'id']],
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
            'or_owner_user_id' => 'Owner User',
            'or_created_user_id' => 'Created User',
            'or_updated_user_id' => 'Updated User',
            'or_created_dt' => 'Created Dt',
            'or_updated_dt' => 'Updated Dt',
            'or_profit_amount' => 'Profit amount',
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
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'or_created_user_id',
                'updatedByAttribute' => 'or_updated_user_id',
            ],
        ];
    }

    public function create(CreateOrderDTO $dto): self
	{
		$this->or_gid = self::generateGid();
		$this->or_uid = self::generateUid();
		$this->or_status_id = $dto->status;
		$this->or_lead_id = $dto->leadId;
		$this->or_name = $this->generateName();
		if ($this->orLead && $this->orLead->employee_id) {
			$this->or_owner_user_id = $this->orLead->employee_id;
		}
		if (!$this->or_name && $this->or_lead_id) {
			$this->or_name = $this->generateName();
		}
		$this->updateOrderTotalByCurrency();

		return $this;
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

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getProductQuotesActive(): ActiveQuery
    {
        return $this->hasMany(ProductQuote::class, ['pq_order_id' => 'or_id'])
            ->where(['not', ['pq_status_id' => ProductQuoteStatus::CANCEL_GROUP]]);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductQuotes(): ActiveQuery
    {
        return $this->hasMany(ProductQuote::class, ['pq_order_id' => 'or_id']);
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
    public function generateName(): string
    {
        $count = self::find()->where(['or_lead_id' => $this->or_lead_id])->count();
        return 'Order ' . ($count + 1);
    }

    /**
     * @return float
     */
    public function getOrderTotalCalcSum(): float
    {
        $sum = 0;
		$quotes = $this->productQuotes;
		if($quotes) {
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

        $this->or_client_total = round($this->or_app_total * $this->or_client_currency_rate, 2);
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

    public function isProcessing()
	{
		return $this->or_status_id === OrderStatus::PROCESSING;
	}

    public function processing(): void
	{
		// ToDo: need to log status
		if (!$this->isProcessing()) {
			OrderStatus::guard($this->or_status_id, OrderStatus::PROCESSING);
			foreach ($this->productQuotes as $productQuote) {
				if (OrderStatus::guardOrder(OrderStatus::PROCESSING, $productQuote->pq_status_id)) {
					$this->setStatus(OrderStatus::PROCESSING);
					break;
				}
			}
		}
	}

	private function setStatus(int $status): void
	{
		if (!array_key_exists($status, OrderStatus::getList())) {
			throw new \InvalidArgumentException('Invalid Status');
		}
		OrderStatus::guard($this->or_status_id, $status);

		$this->or_status_id = $status;
	}
}

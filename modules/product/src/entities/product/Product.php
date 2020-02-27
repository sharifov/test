<?php

namespace modules\product\src\entities\product;

use common\models\Employee;
use common\models\Lead;
use modules\product\src\entities\product\events\ProductClientBudgetChangedEvent;
use modules\product\src\entities\product\events\ProductMarketPriceChangedEvent;
use modules\product\src\entities\product\serializer\ProductSerializer;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productType\ProductType;
use modules\flight\models\Flight;
use modules\hotel\models\Hotel;
use modules\product\src\entities\product\events\ProductCreateEvent;
use modules\product\src\interfaces\Productable;
use modules\product\src\useCases\product\create\ProductCreateForm;
use sales\entities\EventTrait;
use sales\entities\serializer\Serializable;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product".
 *
 * @property int $pr_id
 * @property int $pr_type_id
 * @property string|null $pr_name
 * @property int $pr_lead_id
 * @property string|null $pr_description
 * @property int|null $pr_status_id
 * @property float|null $pr_service_fee_percent
 * @property int|null $pr_created_user_id
 * @property int|null $pr_updated_user_id
 * @property string|null $pr_created_dt
 * @property string|null $pr_updated_dt
 * @property $pr_market_price
 * @property $pr_client_budget
 *
 * @property Flight[] $flights
 * @property Flight $flight
 * @property Hotel[] $hotels
 * @property Hotel $hotel
 * @property Employee $prCreatedUser
 * @property Lead $prLead
 * @property ProductType $prType
 * @property Employee $prUpdatedUser
 * @property ProductQuote[] $productQuotes
 *
 * @property Productable|null $childProduct
 */
class Product extends \yii\db\ActiveRecord implements Serializable
{
    use EventTrait;

    private $childProduct;

    public static function create(ProductCreateForm $form): self
    {
        $product = new static();
        $product->pr_lead_id = $form->pr_lead_id;
        $product->pr_type_id = $form->pr_type_id;
        $product->pr_name = $form->pr_name;
        $product->pr_description = $form->pr_description;
        $product->recordEvent(new ProductCreateEvent($product));
        return $product;
    }

    public function updateInfo(?string $name, ?string $description)
    {
        $this->pr_name = $name;
        $this->pr_description = $description;
    }

    public function changeMarketPrice($value)
    {
        if ($this->pr_market_price !== $value) {
            $this->recordEvent(new ProductMarketPriceChangedEvent($this));
        }
        $this->pr_market_price = $value;
    }

    public function changeClientBudget($value)
    {
        if ($this->pr_client_budget !== $value) {
            $this->recordEvent(new ProductClientBudgetChangedEvent($this));
        }
        $this->pr_client_budget = $value;
    }

    public function isFlight(): bool
    {
        return $this->pr_type_id === ProductType::PRODUCT_FLIGHT;
    }

    public function isHotel(): bool
    {
        return $this->pr_type_id === ProductType::PRODUCT_HOTEL;
    }

    public function getChildProduct(): ?Productable
    {
        if ($this->childProduct !== null) {
            return $this->childProduct;
        }

        $finder = [ProductClasses::getClass($this->pr_type_id), 'findByProduct'];
        $this->childProduct =  $finder($this->pr_id);
        return $this->childProduct;
    }

    public static function tableName(): string
    {
        return '{{%product}}';
    }

    public function rules(): array
    {
        return [
            ['pr_type_id', 'required'],
            ['pr_type_id', 'integer'],
            ['pr_type_id', 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['pr_type_id' => 'pt_id']],

            ['pr_lead_id', 'required'],
            ['pr_lead_id', 'integer'],
            ['pr_lead_id', 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['pr_lead_id' => 'id']],

            ['pr_status_id', 'integer'],

            ['pr_created_user_id', 'integer'],
            ['pr_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pr_created_user_id' => 'id']],

            ['pr_updated_user_id', 'integer'],
            ['pr_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pr_updated_user_id' => 'id']],

            ['pr_description', 'string'],

            ['pr_service_fee_percent', 'number'],

            ['pr_name', 'string', 'max' => 40],

            ['pr_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['pr_updated_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['pr_market_price', 'number'],
            ['pr_client_budget', 'number'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'pr_id' => 'ID',
            'pr_type_id' => 'Type',
            'pr_name' => 'Name',
            'pr_lead_id' => 'Lead ID',
            'pr_description' => 'Description',
            'pr_status_id' => 'Status ID',
            'pr_service_fee_percent' => 'Service Fee Percent',
            'pr_created_user_id' => 'Created User',
            'pr_updated_user_id' => 'Updated User',
            'pr_created_dt' => 'Created Dt',
            'pr_updated_dt' => 'Updated Dt',
            'pr_market_price' => 'Market price',
            'pr_client_budget' => 'Client budget',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pr_created_dt', 'pr_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pr_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'pr_created_user_id',
                'updatedByAttribute' => 'pr_updated_user_id',
            ],
        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getFlights(): ActiveQuery
    {
        return $this->hasMany(Flight::class, ['fl_product_id' => 'pr_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFlight(): ActiveQuery
    {
        return $this->hasOne(Flight::class, ['fl_product_id' => 'pr_id'])->orderBy(['fl_id' => SORT_DESC])->limit(1);
    }

    /**
     * @return ActiveQuery
     */
    public function getHotels(): ActiveQuery
    {
        return $this->hasMany(Hotel::class, ['ph_product_id' => 'pr_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHotel(): ActiveQuery
    {
        return $this->hasOne(Hotel::class, ['ph_product_id' => 'pr_id'])->orderBy(['ph_id' => SORT_DESC])->limit(1);
    }

    /**
     * @return ActiveQuery
     */
    public function getPrCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pr_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPrLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'pr_lead_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPrType(): ActiveQuery
    {
        return $this->hasOne(ProductType::class, ['pt_id' => 'pr_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPrUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pr_updated_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProductQuotes(): ActiveQuery
    {
        return $this->hasMany(ProductQuote::class, ['pq_product_id' => 'pr_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

	/**
	 * @param int $employeeId
	 * @return bool
	 */
	public function canAgentEdit(int $employeeId): bool
	{
		return ($this->prLead->employee_id && $this->prLead->employee_id === $employeeId);
	}

	public function serialize(): array
    {
        return (new ProductSerializer($this))->getData();
    }

    /**
     * @param int $leadId
     * @param array $typeIds
     * @return array|ActiveRecord[]
     */
    public function getByLeadAndType(int $leadId, array $typeIds = ProductType::ALLOW_CALL_EXPERT)
    {
        return self::find()->where(['pr_lead_id' => $leadId])
            ->andWhere(['IN', 'pr_type_id', $typeIds])
            ->all();
    }
}

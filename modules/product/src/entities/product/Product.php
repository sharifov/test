<?php

namespace modules\product\src\entities\product;

use common\models\Employee;
use common\models\Lead;
use common\models\Project;
use modules\cruise\src\entity\cruise\Cruise;
use modules\product\src\entities\product\dto\CreateDto;
use modules\product\src\entities\product\events\ProductClientBudgetChangedEvent;
use modules\product\src\entities\product\events\ProductClonedEvent;
use modules\product\src\entities\product\events\ProductMarketPriceChangedEvent;
use modules\product\src\entities\product\serializer\ProductSerializer;
use modules\product\src\entities\productHolder\ProductHolder;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productType\ProductType;
use modules\flight\models\Flight;
use modules\hotel\models\Hotel;
use modules\attraction\models\Attraction;
use modules\product\src\entities\product\events\ProductCreateEvent;
use modules\product\src\interfaces\Productable;
use modules\rentCar\src\entity\rentCar\RentCar;
use src\entities\EventTrait;
use src\entities\serializer\Serializable;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product".
 *
 * @property int $pr_id
 * @property string $pr_gid [varchar(32)]
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
 * @property int|null $pr_project_id [int]
 *
 * @property Attraction[] $attractions
 * @property Attraction $attraction
 * @property Flight[] $flights
 * @property Flight $flight
 * @property Hotel[] $hotels
 * @property Hotel $hotel
 * @property Cruise[] $cruises
 * @property Cruise $cruise
 * @property Employee $prCreatedUser
 * @property Lead $prLead
 * @property ProductType $prType
 * @property Employee $prUpdatedUser
 * @property ProductQuote[] $productQuotes
 * @property RentCar $rentCar
 * @property RentCar[] $rentCars
 * @property ProductHolder $holder
 *
 * @property Productable|null $childProduct
 * @property Project $project
 */
class Product extends \yii\db\ActiveRecord implements Serializable
{
    use EventTrait;

    private $childProduct;

    public static function create(CreateDto $dto): self
    {
        $product = new static();
        $product->pr_gid = self::generateGid();
        $product->pr_lead_id = $dto->pr_lead_id;
        $product->pr_type_id = $dto->pr_type_id;
        $product->pr_name = $dto->pr_name;
        $product->pr_description = $dto->pr_description;
        $product->pr_project_id = $dto->pr_project_id;
        $product->recordEvent(new ProductCreateEvent($product));
        return $product;
    }

    public static function clone(Product $product, int $leadId, ?int $createdUserId): self
    {
        $clone = new static();
        $clone->pr_gid = self::generateGid();
        $clone->pr_type_id = $product->pr_type_id;
        $clone->pr_name = $product->pr_name;
        $clone->pr_lead_id = $leadId;
        $clone->pr_description = $product->pr_description;
        $clone->pr_status_id = $product->pr_status_id;
        $clone->pr_service_fee_percent = $product->pr_service_fee_percent;
        $clone->pr_created_user_id = $createdUserId;
        $clone->pr_market_price = $product->pr_market_price;
        $clone->pr_client_budget = $product->pr_client_budget;
        $clone->pr_project_id = $product->pr_project_id;
        $clone->recordEvent(new ProductClonedEvent($product));
        return $clone;
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

    public function isAttraction(): bool
    {
        return $this->pr_type_id === ProductType::PRODUCT_ATTRACTION;
    }

    public function isRenTCar(): bool
    {
        return $this->pr_type_id === ProductType::PRODUCT_RENT_CAR;
    }

    public function isCruise(): bool
    {
        return $this->pr_type_id === ProductType::PRODUCT_CRUISE;
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
            ['pr_gid', 'required'],
            ['pr_gid', 'string', 'max' => 32],
            ['pr_gid', 'unique'],

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

            ['pr_service_fee_percent', 'number', 'max' => 100],

            ['pr_name', 'string', 'max' => 40],

            ['pr_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['pr_updated_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['pr_market_price', 'number'],
            ['pr_client_budget', 'number'],

            ['pr_project_id', 'integer'],
            [['pr_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['pr_project_id' => 'id']],
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
            'pr_project_id' => 'Project',
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
//            'user' => [
//                'class' => BlameableBehavior::class,
//                'createdByAttribute' => 'pr_created_user_id',
//                'updatedByAttribute' => 'pr_updated_user_id',
//            ],
        ];
    }

    public function getAttractions(): ActiveQuery
    {
        return $this->hasMany(Attraction::class, ['atn_product_id' => 'pr_id']);
    }

    public function getAttraction(): ActiveQuery
    {
        return $this->hasOne(Attraction::class, ['atn_product_id' => 'pr_id'])->orderBy(['atn_id' => SORT_DESC])->limit(1);
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

    public function getHolder(): ActiveQuery
    {
        return $this->hasOne(ProductHolder::class, ['ph_product_id' => 'pr_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHotel(): ActiveQuery
    {
        return $this->hasOne(Hotel::class, ['ph_product_id' => 'pr_id'])->orderBy(['ph_id' => SORT_DESC])->limit(1);
    }

    public function getRentCars(): ActiveQuery
    {
        return $this->hasMany(RentCar::class, ['prc_product_id' => 'pr_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRentCar(): ActiveQuery
    {
        return $this->hasOne(RentCar::class, ['prc_product_id' => 'pr_id'])->orderBy(['prc_id' => SORT_DESC])->limit(1);
    }

    /**
     * @return ActiveQuery
     */
    public function getCruises(): ActiveQuery
    {
        return $this->hasMany(Cruise::class, ['crs_product_id' => 'pr_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCruise(): ActiveQuery
    {
        return $this->hasOne(Cruise::class, ['crs_product_id' => 'pr_id'])->orderBy(['crs_id' => SORT_DESC])->limit(1);
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

    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'pr_project_id']);
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

    /**
     * @return string
     */
    public function getIconClass(): string
    {
        return ($this->prType && $this->prType->pt_icon_class) ? $this->prType->pt_icon_class : '';
    }

    public function isDeletable(): bool
    {
        if (!$this->productQuotes) {
            return true;
        }
        foreach ($this->productQuotes as $productQuote) {
            if (!$productQuote->isDeletable()) {
                return false;
            }
        }
        return true;
    }

    public function getNotDeletableProductQuotes(): array
    {
        $result = [];
        if (!$this->productQuotes) {
            return $result;
        }
        foreach ($this->productQuotes as $productQuote) {
            if (!$productQuote->isDeletable()) {
                $result[$productQuote->pq_id] = $productQuote->pq_gid;
            }
        }
        return $result;
    }

    public static function generateGid(): string
    {
        return md5(uniqid('fq', true));
    }
}

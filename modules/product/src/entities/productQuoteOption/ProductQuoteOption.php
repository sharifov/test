<?php

namespace modules\product\src\entities\productQuoteOption;

use common\components\validators\CheckJsonValidator;
use common\models\Employee;
use modules\product\src\entities\productOption\ProductOption;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuote\serializer\ProductQuoteSerializer;
use modules\product\src\entities\productQuoteOption\events\ProductQuoteOptionCloneCreatedEvent;
use modules\product\src\entities\productQuoteOption\serializer\ProductQuoteOptionSerializer;
use src\entities\EventTrait;
use src\entities\serializer\Serializable;
use src\helpers\product\ProductQuoteHelper;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_quote_option".
 *
 * @property int $pqo_id
 * @property int $pqo_product_quote_id
 * @property int|null $pqo_product_option_id
 * @property string $pqo_name
 * @property string|null $pqo_description
 * @property int|null $pqo_status_id
 * @property float|null $pqo_price
 * @property float|null $pqo_client_price
 * @property float|null $pqo_extra_markup
 * @property int|null $pqo_created_user_id
 * @property int|null $pqo_updated_user_id
 * @property string|null $pqo_created_dt
 * @property string|null $pqo_updated_dt
 * @property string $pqo_request_data [json]
 *
 * @property Employee $pqoCreatedUser
 * @property ProductOption $pqoProductOption
 * @property ProductQuote $pqoProductQuote
 * @property Employee $pqoUpdatedUser
 */
class ProductQuoteOption extends ActiveRecord implements Serializable
{
    use EventTrait;

    public static function clone(ProductQuoteOption $option, int $productQuoteId): self
    {
        $clone = new self();

        $clone->attributes = $option->attributes;

        $clone->pqo_id = null;
        $clone->pqo_product_quote_id = $productQuoteId;
        $clone->pqo_status_id = ProductQuoteOptionStatus::PENDING;
        $clone->recordEvent(new ProductQuoteOptionCloneCreatedEvent($clone));

        return $clone;
    }

    public static function copy(ProductQuoteOption $option, int $productQuoteId): self
    {
        $clone = new self();
        $clone->attributes = $option->attributes;
        $clone->pqo_id = null;
        $clone->pqo_product_quote_id = $productQuoteId;
        $clone->pqo_status_id = ProductQuoteOptionStatus::PENDING;
        return $clone;
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'product_quote_option';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['pqo_product_quote_id'], 'required'],
            [['pqo_product_quote_id', 'pqo_product_option_id', 'pqo_status_id', 'pqo_created_user_id', 'pqo_updated_user_id'], 'integer'],
            [['pqo_description'], 'string'],
            [['pqo_price', 'pqo_client_price', 'pqo_extra_markup'], 'number'],
            [['pqo_created_dt', 'pqo_updated_dt'], 'safe'],
            [['pqo_name'], 'string', 'max' => 50],
            [['pqo_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqo_created_user_id' => 'id']],
            [['pqo_product_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductOption::class, 'targetAttribute' => ['pqo_product_option_id' => 'po_id']],
            [['pqo_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['pqo_product_quote_id' => 'pq_id']],
            [['pqo_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pqo_updated_user_id' => 'id']],
            [['pqo_request_data'], CheckJsonValidator::class],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'pqo_id' => 'ID',
            'pqo_product_quote_id' => 'Product Quote ID',
            'pqoProductQuote' => 'Product Quote',
            'pqo_product_option_id' => 'Product Option ID',
            'pqo_name' => 'Name',
            'pqo_description' => 'Description',
            'pqo_status_id' => 'Status',
            'pqo_price' => 'Price',
            'pqo_client_price' => 'Client Price',
            'pqo_extra_markup' => 'Extra Markup',
            'pqo_created_user_id' => 'Created User',
            'pqo_updated_user_id' => 'Updated User',
            'pqo_created_dt' => 'Created Dt',
            'pqo_updated_dt' => 'Updated Dt',
        ];
    }

    public function afterFind(): void
    {
        parent::afterFind();
        $this->pqo_price                     = $this->pqo_price === null ? null : (float) $this->pqo_price;
        $this->pqo_client_price              = $this->pqo_client_price === null ? null : (float) $this->pqo_client_price;
        $this->pqo_extra_markup              = $this->pqo_extra_markup === null ? null : (float) $this->pqo_extra_markup;
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['pqo_created_dt', 'pqo_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['pqo_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
//            'user' => [
//                'class' => BlameableBehavior::class,
//                'createdByAttribute' => 'pqo_created_user_id',
//                'updatedByAttribute' => 'pqo_updated_user_id',
//            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPqoCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pqo_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPqoProductOption(): ActiveQuery
    {
        return $this->hasOne(ProductOption::class, ['po_id' => 'pqo_product_option_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPqoProductQuote(): ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'pqo_product_quote_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPqoUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'pqo_updated_user_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public function serialize(): array
    {
        return (new ProductQuoteOptionSerializer($this))->getData();
    }

    public function calculateClientPrice(): void
    {
        $currencyRate = $this->pqoProductQuote->pq_client_currency_rate;
        $clientPrice = is_numeric($this->pqo_price) ? $this->pqo_price : 0.00;
        $clientPrice = is_numeric($this->pqo_extra_markup) ? $clientPrice + $this->pqo_extra_markup : $clientPrice;
        $this->pqo_client_price = ProductQuoteHelper::roundPrice($clientPrice * $currencyRate);
    }

    public static function create(
        int $productQuoteId,
        int $productOptionId,
        string $name,
        ?string $description,
        float $price,
        float $clientPrice,
        ?float $extraMarkup,
        ?string $requestData
    ): self {
        $option = new self();
        $option->pqo_product_quote_id = $productQuoteId;
        $option->pqo_product_option_id = $productOptionId;
        $option->pqo_name = $name;
        $option->pqo_description = $description;
        $option->pqo_price = $price;
        $option->pqo_client_price = $clientPrice;
        $option->pqo_extra_markup = $extraMarkup;
        $option->pqo_request_data = $requestData;
        return $option;
    }

    public function pending(): void
    {
        $this->pqo_status_id = ProductQuoteOptionStatus::PENDING;
    }

    public function inProgress(): void
    {
        $this->pqo_status_id = ProductQuoteOptionStatus::IN_PROGRESS;
    }

    public function error(): void
    {
        $this->pqo_status_id = ProductQuoteOptionStatus::ERROR;
    }

    public function done(): void
    {
        $this->pqo_status_id = ProductQuoteOptionStatus::DONE;
    }

    public function canceled(): void
    {
        $this->pqo_status_id = ProductQuoteOptionStatus::CANCELED;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return \modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus::getName($this->pqo_status_id);
    }
}

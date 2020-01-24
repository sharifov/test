<?php

namespace common\models;

use common\models\query\ProductQuery;
use modules\flight\models\Flight;
use modules\hotel\models\Hotel;
use modules\product\src\entities\product\events\ProductCreateEvent;
use modules\product\src\useCases\product\create\ProductCreateForm;
use sales\entities\EventTrait;
use Yii;
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
 *
 * @property Flight[] $flights
 * @property Flight $flight
 * @property Hotel[] $hotels
 * @property Hotel $hotel
 * @property Employee $prCreatedUser
 * @property Lead $prLead
 * @property ProductType $prType
 * @property Employee $prUpdatedUser
 * @property array $extraData
 * @property ProductQuote[] $productQuotes
 */
class Product extends \yii\db\ActiveRecord
{
    use EventTrait;

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

    public static function tableName(): string
    {
        return 'product';
    }

    public function rules(): array
    {
        return [
            [['pr_type_id', 'pr_lead_id'], 'required'],
            [['pr_type_id', 'pr_lead_id', 'pr_status_id', 'pr_created_user_id', 'pr_updated_user_id'], 'integer'],
            [['pr_description'], 'string'],
            [['pr_service_fee_percent'], 'number'],
            [['pr_created_dt', 'pr_updated_dt'], 'safe'],
            [['pr_name'], 'string', 'max' => 40],
            [['pr_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pr_created_user_id' => 'id']],
            [['pr_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['pr_lead_id' => 'id']],
            [['pr_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductType::class, 'targetAttribute' => ['pr_type_id' => 'pt_id']],
            [['pr_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['pr_updated_user_id' => 'id']],

        ];
    }

    public function extraFields(): array
    {
        return [
            //'pr_id',
            'pr_type_id',
            'pr_name',
            'pr_lead_id',
            'pr_description',
            'pr_status_id',
            'pr_service_fee_percent',
//            'pr_created_user_id',
//            'pr_updated_user_id',
//            'pr_created_dt',
//            'pr_updated_dt',
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

    /**
     * {@inheritdoc}
     * @return ProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductQuery(static::class);
    }


    /**
     * @return array
     */
    public function getExtraData(): array
    {
        return array_intersect_key($this->attributes, array_flip($this->extraFields()));
    }

	/**
	 * @param int $employeeId
	 * @return bool
	 */
	public function canAgentEdit(int $employeeId): bool
	{
		return ($this->prLead->employee_id && $this->prLead->employee_id === $employeeId);
	}

}

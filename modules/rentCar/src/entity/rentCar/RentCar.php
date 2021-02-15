<?php

namespace modules\rentCar\src\entity\rentCar;

use DateTime;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\interfaces\Productable;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\serializer\RentCarSerializer;
use sales\auth\Auth;
use sales\entities\EventTrait;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rent_car".
 *
 * @property int $prc_id
 * @property int|null $prc_product_id
 * @property string|null $prc_pick_up_code
 * @property string|null $prc_drop_off_code
 * @property string|null $prc_pick_up_date
 * @property string|null $prc_drop_off_date
 * @property string|null $prc_pick_up_time
 * @property string|null $prc_drop_off_time
 * @property string|null $prc_created_dt
 * @property string|null $prc_updated_dt
 * @property int|null $prc_created_user_id
 * @property int|null $prc_updated_user_id
 * @property string|null $prc_request_hash_key
 *
 * @property Product $prcProduct
 * @property RentCarQuote[] $rentCarQuotes
 */
class RentCar extends ActiveRecord implements Productable
{
    use EventTrait;

    public const FORMAT_DT = 'Y-m-d H:i';

    public function rules(): array
    {
        return [
            ['prc_product_id', 'required'],

            [['prc_pick_up_code', 'prc_drop_off_code'], 'string', 'max' => 10],
            ['prc_request_hash_key', 'string', 'max' => 32],

            [['prc_pick_up_date', 'prc_drop_off_date'], 'safe'],

            [['prc_pick_up_time', 'prc_drop_off_time'], 'safe'],

            ['prc_product_id', 'integer'],
            ['prc_product_id', 'exist', 'skipOnError' => true, 'targetClass' => Product::class, 'targetAttribute' => ['prc_product_id' => 'pr_id']],

            [['prc_created_dt', 'prc_updated_dt'], 'safe'],

            [['prc_created_user_id', 'prc_updated_user_id'], 'integer'],
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['prc_created_dt', 'prc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['prc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'prc_created_user_id',
                'updatedByAttribute' => 'prc_updated_user_id',
            ],
        ];
    }

    public function getPrcProduct(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Product::class, ['pr_id' => 'prc_product_id']);
    }

    public function getRentCarQuotes(): \yii\db\ActiveQuery
    {
        return $this->hasMany(RentCarQuote::class, ['rcq_rent_car_id' => 'prc_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'prc_id' => 'ID',
            'prc_product_id' => 'Product ID',
            'prc_pick_up_code' => 'Pick Up Code',
            'prc_drop_off_code' => 'Drop Off Code',
            'prc_pick_up_date' => 'Pick Up Date',
            'prc_drop_off_date' => 'Drop Off Date',
            'prc_pick_up_time' => 'Pick Up Time',
            'prc_drop_off_time' => 'Drop Off Time',
            'prc_created_dt' => 'Created Dt',
            'prc_updated_dt' => 'Updated Dt',
            'prc_created_user_id' => 'Created User ID',
            'prc_updated_user_id' => 'Updated User ID',
            'prc_request_hash_key' => 'Request hash key'
        ];
    }

    public static function find(): RentCarScopes
    {
        return new RentCarScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'rent_car';
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            $request_hash_key = $this->generateRequestHashKey();
            if ($this->prc_request_hash_key !== $request_hash_key) {
                $this->prc_request_hash_key = $request_hash_key;
            }
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (isset($changedAttributes['prc_request_hash_key'])) {
            $this->updateInvalidRequestQuotes();
        }
    }

    public function updateInvalidRequestQuotes(): void
    {
        if ($this->rentCarQuotes) {
            foreach ($this->rentCarQuotes as $quote) {
                if (
                    $quote->rcq_request_hash_key !== $this->prc_request_hash_key &&
                    $quote->rcqProductQuote && $quote->rcqProductQuote->pq_status_id !== ProductQuoteStatus::DELIVERED
                ) {
                    $creatorId = Auth::id();
                    $description = 'Find invalid request quotes and update status';
                    $quote->rcqProductQuote->declined($creatorId, $description);
                    $quote->rcqProductQuote->save();
                }
            }
        }
    }

    public function generateRequestHashKey(): string
    {
        $sourceKey = $this->prc_pick_up_code . '|' .
            $this->prc_drop_off_code . '|' .
            $this->prc_pick_up_date . '|' .
            $this->prc_drop_off_date . '|' .
            $this->prc_pick_up_time . '|' .
            $this->prc_drop_off_time;

        return md5($sourceKey);
    }

    public static function create(int $productId): self
    {
        $model = new static();
        $model->prc_product_id = $productId;
        return $model;
    }

    public function getId(): int
    {
        return $this->ph_id;
    }

    public function serialize(): array
    {
        return (new RentCarSerializer($this))->getData();
    }

    public static function findByProduct(int $productId): ?Productable
    {
        return self::find()->byProduct($productId)->one();
    }

    /**
     * @return DateTime|false
     */
    public function generatePickUpDate()
    {
        return DateTime::createFromFormat('Y-m-d', $this->prc_pick_up_date);
    }

    /**
     * @return DateTime|false
     */
    public function generateDropOffDate()
    {
        return DateTime::createFromFormat('Y-m-d', $this->prc_drop_off_date);
    }

    public function calculateDays(): int
    {
        if (($pickUpDt = $this->generatePickUpDate()) && ($dropOffDt = $this->generateDropOffDate())) {
            if ($days = $pickUpDt->diff($dropOffDt)->days) {
                return (int) $days + 1;
            }
        }
        return 1;
    }
}

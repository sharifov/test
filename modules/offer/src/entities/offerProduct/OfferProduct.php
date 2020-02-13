<?php

namespace modules\offer\src\entities\offerProduct;

use common\models\Employee;
use modules\offer\src\entities\offer\events\OfferRecalculateProfitAmountEvent;
use modules\offer\src\entities\offer\Offer;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\entities\EventTrait;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use yii\db\StaleObjectException;

/**
 * This is the model class for table "offer_product".
 *
 * @property int $op_offer_id
 * @property int $op_product_quote_id
 * @property int $op_created_user_id
 * @property string $op_created_dt
 *
 * @property Employee $opCreatedUser
 * @property Offer $opOffer
 * @property ProductQuote $opProductQuote
 */
class OfferProduct extends \yii\db\ActiveRecord
{
    use EventTrait;

    public static function tableName(): string
    {
        return 'offer_product';
    }

    public function rules(): array
    {
        return [
            [['op_offer_id', 'op_product_quote_id'], 'required'],
            [['op_offer_id', 'op_product_quote_id', 'op_created_user_id'], 'integer'],
            [['op_created_dt'], 'safe'],
            [['op_offer_id', 'op_product_quote_id'], 'unique', 'targetAttribute' => ['op_offer_id', 'op_product_quote_id']],
            [['op_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['op_created_user_id' => 'id']],
            [['op_offer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Offer::class, 'targetAttribute' => ['op_offer_id' => 'of_id']],
            [['op_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['op_product_quote_id' => 'pq_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'op_offer_id' => 'Offer Id',
            'opOffer' => 'Offer',
            'op_product_quote_id' => 'Product Quote Id',
            'opProductQuote' => 'Product Quote',
            'op_created_user_id' => 'Created User',
            'opCreatedUser' => 'Created User',
            'op_created_dt' => 'Created Dt',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['op_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'op_created_user_id',
                'updatedByAttribute' => 'op_created_user_id',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOpCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'op_created_user_id']);
    }

    public function getOpOffer(): ActiveQuery
    {
        return $this->hasOne(Offer::class, ['of_id' => 'op_offer_id']);
    }

    public function getOpProductQuote(): ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'op_product_quote_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public function prepareRemove(): void
    {
       $this->recordEvent(new OfferRecalculateProfitAmountEvent($this->opOffer));
    }

}

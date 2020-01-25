<?php

namespace common\models;

use common\models\query\OfferProductQuery;
use modules\product\src\entities\productQuote\ProductQuote;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'offer_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'op_offer_id' => 'Offer ID',
            'op_product_quote_id' => 'Product Quote ID',
            'op_created_user_id' => 'Created User ID',
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
     * @return \yii\db\ActiveQuery
     */
    public function getOpCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'op_created_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpOffer()
    {
        return $this->hasOne(Offer::class, ['of_id' => 'op_offer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpProductQuote()
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'op_product_quote_id']);
    }

    /**
     * {@inheritdoc}
     * @return OfferProductQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OfferProductQuery(static::class);
    }
}

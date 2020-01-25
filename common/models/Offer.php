<?php

namespace common\models;

use common\models\query\OfferQuery;
use modules\product\src\entities\productQuote\ProductQuote;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "offer".
 *
 * @property int $of_id
 * @property string $of_gid
 * @property string|null $of_uid
 * @property string|null $of_name
 * @property int $of_lead_id
 * @property int|null $of_status_id
 * @property int|null $of_owner_user_id
 * @property int|null $of_created_user_id
 * @property int|null $of_updated_user_id
 * @property string|null $of_created_dt
 * @property string|null $of_updated_dt
 * @property string|null $of_client_currency
 * @property float|null $of_client_currency_rate
 * @property float|null $of_app_total
 * @property float|null $of_client_total
 *
 * @property Currency $ofClientCurrency
 * @property Employee $ofCreatedUser
 * @property Lead $ofLead
 * @property Employee $ofOwnerUser
 * @property Employee $ofUpdatedUser
 * @property OfferProduct[] $offerProducts
 * @property string $statusLabel
 * @property string $className
 * @property string $statusName
 * @property float $offerTotalCalcSum
 * @property array $communicationData
 * @property ProductQuote[] $opProductQuotes
 */
class Offer extends \yii\db\ActiveRecord
{

    public const STATUS_NEW         = 1;
    public const STATUS_SENT        = 2;
    public const STATUS_APPLY       = 3;

    public const STATUS_LIST = [
        self::STATUS_NEW            => 'New',
        self::STATUS_SENT           => 'Sent',
        self::STATUS_APPLY          => 'Apply',
    ];

    public const STATUS_CLASS_LIST        = [
        self::STATUS_NEW            => 'info',
        self::STATUS_SENT           => 'warning',
        self::STATUS_APPLY          => 'success',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'offer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['of_gid', 'of_lead_id'], 'required'],
            [['of_lead_id', 'of_status_id', 'of_owner_user_id', 'of_created_user_id', 'of_updated_user_id'], 'integer'],
            [['of_created_dt', 'of_updated_dt'], 'safe'],
            [['of_client_currency_rate', 'of_app_total', 'of_client_total'], 'number'],
            [['of_gid'], 'string', 'max' => 32],
            [['of_uid'], 'string', 'max' => 15],
            [['of_name'], 'string', 'max' => 40],
            [['of_client_currency'], 'string', 'max' => 3],
            [['of_gid'], 'unique'],
            [['of_uid'], 'unique'],
            [['of_client_currency'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::class, 'targetAttribute' => ['of_client_currency' => 'cur_code']],
            [['of_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['of_created_user_id' => 'id']],
            [['of_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['of_lead_id' => 'id']],
            [['of_owner_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['of_owner_user_id' => 'id']],
            [['of_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['of_updated_user_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function extraFields(): array
    {
        return [
            //'of_id',
            'of_gid',
            'of_uid',
            'of_name',
            'of_lead_id',
            'of_status_id',
//            'of_owner_user_id',
//            'of_created_user_id',
//            'of_updated_user_id',
//            'of_created_dt',
//            'of_updated_dt',
            'of_client_currency',
            'of_client_currency_rate',
            'of_app_total',
            'of_client_total',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'of_id' => 'ID',
            'of_gid' => 'Gid',
            'of_uid' => 'Uid',
            'of_name' => 'Name',
            'of_lead_id' => 'Lead ID',
            'of_status_id' => 'Status ID',
            'of_owner_user_id' => 'Owner User ID',
            'of_created_user_id' => 'Created User ID',
            'of_updated_user_id' => 'Updated User ID',
            'of_created_dt' => 'Created Dt',
            'of_updated_dt' => 'Updated Dt',
            'of_client_currency' => 'Client Currency',
            'of_client_currency_rate' => 'Client Currency Rate',
            'of_app_total' => 'App Total',
            'of_client_total' => 'Client Total',
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['of_created_dt', 'of_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['of_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'of_created_user_id',
                'updatedByAttribute' => 'of_updated_user_id',
            ],
        ];
    }


    public function afterFind()
    {
        parent::afterFind();
        $this->of_client_currency_rate      = $this->of_client_currency_rate === null ? null : (float) $this->of_client_currency_rate;
        $this->of_app_total                 = $this->of_app_total === null ? null : (float) $this->of_app_total;
        $this->of_client_total              = $this->of_client_total === null ? null : (float) $this->of_client_total;
    }

    /**
     * Offer init create
     */
    public function initCreate(): void
    {
        $this->of_gid = self::generateGid();
        $this->of_uid = self::generateUid();
        $this->of_status_id = self::STATUS_NEW;
    }

    /**
     * @return ActiveQuery
     */
    public function getOfClientCurrency(): ActiveQuery
    {
        return $this->hasOne(Currency::class, ['cur_code' => 'of_client_currency']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOfCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'of_created_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOfLead(): ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'of_lead_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOfOwnerUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'of_owner_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOfUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'of_updated_user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOfferProducts(): ActiveQuery
    {
        return $this->hasMany(OfferProduct::class, ['op_offer_id' => 'of_id']);
    }

    /**
     * @return ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getOpProductQuotes(): ActiveQuery
    {
        return $this->hasMany(ProductQuote::class, ['of_id' => 'op_product_quote_id'])->viaTable('offer_product', ['op_offer_id' => 'of_id']);
    }

    /**
     * @return OfferQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OfferQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        return self::STATUS_LIST;
    }

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return self::STATUS_LIST[$this->of_status_id] ?? '';
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return self::STATUS_CLASS_LIST[$this->of_status_id] ?? '';
    }

    /**
     * @return string
     */
    public function getStatusLabel(): string
    {
        return Html::tag('span', $this->getStatusName(), ['class' => 'badge badge-' . $this->getClassName()]);
    }

    /**
     * @return string
     */
    public static function generateGid(): string
    {
        return md5(uniqid('of', true));
    }

    /**
     * @return string
     */
    public static function generateUid(): string
    {
        return uniqid('of');
    }

    /**
     * @return string
     */
    public function generateName(): string
    {
        $count = self::find()->where(['of_lead_id' => $this->of_lead_id])->count();
        return 'Offer ' . ($count + 1);
    }

    /**
     * @return float
     */
    public function getOfferTotalCalcSum(): float
    {
        $sum = 0;
        $offerProducts = $this->offerProducts;
        if ($offerProducts) {
            foreach ($offerProducts as $offerProduct) {
                if ($quote = $offerProduct->opProductQuote) {
                    $sum += $quote->totalCalcSum + $quote->pq_service_fee_sum;
                }
            }
            $sum = round($sum, 2);
        }
        return $sum;
    }

    public function updateOfferTotalByCurrency(): void
    {
        if ($this->ofClientCurrency) {
            $this->of_client_currency_rate = (float) $this->ofClientCurrency->cur_app_rate;
        }

        $this->of_client_total = round($this->of_app_total * $this->of_client_currency_rate, 2);
    }

    /**
     * @return array
     */
    public function getCommunicationData(): array
    {
        $data = $this->extraData;

        $offerProducts = $this->offerProducts;
        if ($offerProducts) {
            foreach ($offerProducts as $offerProduct) {
                if ($quote = $offerProduct->opProductQuote) {

                    $quoteData = $quote->extraData;
                    $quoteData['product'] = $quote->pqProduct->extraData;

                    $productQuoteOptions = $quote->productQuoteOptions;
                    $productQuoteOptionsData = [];

                    if ($productQuoteOptions) {
                        foreach ($productQuoteOptions as $productQuoteOption) {
                            $productQuoteOptionsData[] = $productQuoteOption->extraData;
                        }
                    }

                    //$quoteData['productQuoteData'] = $quote->extraData;
                    $quoteData['productQuoteOptions'] = $productQuoteOptionsData;

                    $data['quotes'][] = $quoteData;
                    //$sum += $quote->totalCalcSum + $quote->pq_service_fee_sum;
                }
            }
            //$sum = round($sum, 2);
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getExtraData(): array
    {
        return array_intersect_key($this->attributes, array_flip($this->extraFields()));
    }
}

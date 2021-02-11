<?php

namespace modules\attraction\models;

use modules\attraction\models\Attraction;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\db\ActiveQuery;
use Yii;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "attraction_quote".
 *
 * @property int $atnq_id
 * @property int $atnq_attraction_id
 * @property string|null $atnq_hash_key
 * @property int|null $atnq_product_quote_id
 * @property string|null $atnq_json_response
 *
 * @property Attraction $atnqAttraction
 * @property ProductQuote $atnqProductQuote
 */
class AttractionQuote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attraction_quote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atnq_attraction_id'], 'required'],
            [['atnq_attraction_id', 'atnq_product_quote_id'], 'integer'],
            [['atnq_json_response'], 'safe'],
            [['atnq_hash_key'], 'string', 'max' => 32],
            [['atnq_hash_key'], 'unique'],
            [['atnq_attraction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attraction::class, 'targetAttribute' => ['atnq_attraction_id' => 'atn_id']],
            [['atnq_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['atnq_product_quote_id' => 'pq_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'atnq_id' => 'ID',
            'atnq_attraction_id' => 'Attraction ID',
            'atnq_hash_key' => 'Hash Key',
            'atnq_product_quote_id' => 'Product Quote ID',
            'atnq_json_response' => 'Json Response',
        ];
    }

    public static function getHashKey(string $roomKey): string
    {
        return md5($roomKey);
    }

    public static function generateGid(): string
    {
        return md5(uniqid('aq', true));
    }

    /**
     * @param array $quoteData
     * @param \modules\attraction\models\Attraction $attractinRequest
     * @param string $currency
     * @return array|AttractionQuote|\yii\db\ActiveRecord|null
     */
    public static function findOrCreateByData(array $quoteData, Attraction $attractionRequest, string $currency = 'USD')
    {
        $aQuote = null;

        if (isset($quoteData['id']) && $quoteId = $quoteData['id']) {
            $totalAmount = 0;
            if (isset($quoteId)) {
                $hashKey = self::getHashKey($quoteId);

                $aQuote = self::find()->where([
                    'atnq_attraction_id' => $attractionRequest->atn_id,
                    'atnq_hash_key' => $hashKey
                ])->one();

                if (!$aQuote) {
                    $prQuote = new ProductQuote();
                    $prQuote->pq_product_id = $attractionRequest->atn_product_id;
                    $prQuote->pq_origin_currency = $currency;
                    //$prQuote->pq_client_currency = ProductQuoteHelper::getClientCurrencyCode($hotelRequest->phProduct);

                    $prQuote->pq_owner_user_id = Yii::$app->user->id;
                    //$prQuote->pq_price = (float)$totalAmount;
                    //$prQuote->pq_origin_price = (float)$totalAmount;
                    //$prQuote->pq_client_price = (float)$totalAmount;
                    $prQuote->pq_status_id = ProductQuoteStatus::PENDING;
                    $prQuote->pq_gid = self::generateGid();
                    $prQuote->pq_service_fee_sum = 0;
                    //$prQuote->pq_client_currency_rate = ProductQuoteHelper::getClientCurrencyRate($hotelRequest->phProduct);
                    $prQuote->pq_origin_currency_rate = 1;
                    $prQuote->pq_name = $quoteData['name'];

                    if ($prQuote->save()) {
                        $aQuote = new self();
                        $aQuote->atnq_hash_key = $hashKey;
                        $aQuote->atnq_attraction_id = $attractionRequest->atn_id;
                        $aQuote->atnq_product_quote_id = $prQuote->pq_id;
                        //$aQuote->hq_hotel_name = $hotelModel->hl_name;
                        //$aQuote->hq_destination_name = $hotelModel->hl_destination_name;
                        //$aQuote->hq_request_hash = $hotelRequest->ph_request_hash_key;

                        if (!$aQuote->save()) {
                            Yii::error(
                                VarDumper::dumpAsString($aQuote->errors),
                                'Model:HotelQuote:findOrCreateByData:HotelQuote:save'
                            );
                        }
                    }
                }
            }
        }

        return $aQuote;
    }

    /**
     * Gets query for [[AtnqAttraction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtnqAttraction(): ActiveQuery
    {
        return $this->hasOne(Attraction::class, ['atn_id' => 'atnq_attraction_id']);
    }

    /**
     * Gets query for [[AtnqProductQuote]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtnqProductQuote(): ActiveQuery
    {
        return $this->hasOne(ProductQuote::className(), ['pq_id' => 'atnq_product_quote_id']);
    }
}

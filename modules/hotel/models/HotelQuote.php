<?php

namespace modules\hotel\models;

use common\models\Client;
use common\models\Currency;
use common\models\Lead;
use common\models\Project;
use modules\hotel\src\entities\hotelQuote\events\HotelQuoteCloneCreatedEvent;
use modules\hotel\src\entities\hotelQuote\serializer\HotelQuoteSerializer;
use modules\hotel\src\services\hotelQuote\HotelQuotePriceCalculator;
use modules\hotel\src\useCases\quote\HotelProductQuoteCreateDto;
use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\hotel\src\entities\hotelQuote\Scopes;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethodQuery;
use modules\product\src\interfaces\ProductDataInterface;
use modules\product\src\interfaces\Quotable;
use sales\entities\EventTrait;
use sales\helpers\product\ProductQuoteHelper;
use sales\services\CurrencyHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "hotel_quote".
 *
 * @property int $hq_id
 * @property int $hq_hotel_id
 * @property string|null $hq_hash_key
 * @property int|null $hq_product_quote_id
 * @property string|null $hq_destination_name
 * @property string $hq_hotel_name
 * @property int|null $hq_hotel_list_id
 * @property string|null $hq_request_hash
 * @property array|null $hq_json_booking
 * @property string|null $hq_booking_id // field "reference" from api response
 * @property array|null $hq_origin_search_data
 * @property string|null $hq_check_in_date
 * @property string|null $hq_check_out_date
 *
 * @property Hotel $hqHotel
 * @property HotelList $hqHotelList
 * @property ProductQuote $hqProductQuote
 * @property HotelQuoteRoom[] $hotelQuoteRooms
 */
class HotelQuote extends ActiveRecord implements Quotable, ProductDataInterface
{
    use EventTrait;

    public const SERVICE_FEE = 0.035;

    public static function clone(HotelQuote $quote, int $hotelId, int $productQuoteId): self
    {
        $clone = new self();

        $clone->attributes = $quote->attributes;
        $clone->hq_id = null;
        $clone->hq_hash_key = null;
        $clone->hq_hotel_id = $hotelId;
        $clone->hq_product_quote_id = $productQuoteId;
        $clone->recordEvent(new HotelQuoteCloneCreatedEvent($clone));

        return $clone;
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'hotel_quote';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['hq_hotel_id', 'hq_hotel_name'], 'required'],
            [['hq_hotel_id', 'hq_product_quote_id', 'hq_hotel_list_id'], 'integer'],
            [['hq_hash_key', 'hq_request_hash'], 'string', 'max' => 32],
            [['hq_destination_name'], 'string', 'max' => 255],
            [['hq_hotel_name'], 'string', 'max' => 200],
            [['hq_hotel_id', 'hq_hash_key'], 'unique', 'targetAttribute' => ['hq_hotel_id', 'hq_hash_key']],
            [['hq_hotel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hotel::class, 'targetAttribute' => ['hq_hotel_id' => 'ph_id']],
            [['hq_hotel_list_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelList::class, 'targetAttribute' => ['hq_hotel_list_id' => 'hl_id']],
            [['hq_product_quote_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductQuote::class, 'targetAttribute' => ['hq_product_quote_id' => 'pq_id']],
            [['hq_json_booking', 'hq_origin_search_data'], 'safe'],

            [['hq_check_in_date', 'hq_check_out_date'], 'date', 'format' => 'php:Y-m-d', 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'hq_id' => 'ID',
            'hq_hotel_id' => 'Hotel ID',
            'hq_hash_key' => 'Hash Key',
            'hq_product_quote_id' => 'Product Quote ID',
            'hq_destination_name' => 'Destination Name',
            'hq_hotel_name' => 'Hotel Name',
            'hq_hotel_list_id' => 'Hotel List ID',
            'hq_request_hash' => 'Request Hash',
            'hq_json_booking' => 'Booking json',
            'hq_origin_search_data' => 'Origin search data',
            'hq_check_in_date' => 'Check in date',
            'hq_check_out_date' => 'Check out date',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getHqHotel(): ActiveQuery
    {
        return $this->hasOne(Hotel::class, ['ph_id' => 'hq_hotel_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHqHotelList(): ActiveQuery
    {
        return $this->hasOne(HotelList::class, ['hl_id' => 'hq_hotel_list_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHqProductQuote(): ActiveQuery
    {
        return $this->hasOne(ProductQuote::class, ['pq_id' => 'hq_product_quote_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHotelQuoteRooms(): ActiveQuery
    {
        return $this->hasMany(HotelQuoteRoom::class, ['hqr_hotel_quote_id' => 'hq_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function getHashKey(string $roomKey): string
    {
        return md5($roomKey);
    }

    /**
     * @param array $quoteData
     * @param HotelList $hotelModel
     * @param Hotel $hotelRequest
     * @param int|null $ownerId
     * @param int|null $orderId
     * @param string $currency
     * @return array|HotelQuote|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function findOrCreateByData(
        array $quoteData,
        HotelList $hotelModel,
        Hotel $hotelRequest,
        ?int $ownerId,
        ?int $orderId,
        string $currency = 'USD'
    ) {
        $hQuote = null;

        if (isset($quoteData['rates']) && $rooms = $quoteData['rates']) {
            $totalAmount = 0;
            if (isset($quoteData['groupKey'])) {
                $hashKey = self::getHashKey($quoteData['groupKey']);

                $hQuote = self::find()->where([
                    'hq_hotel_list_id' => $hotelModel->hl_id,
                    'hq_hotel_id' => $hotelRequest->ph_id,
                    'hq_hash_key' => $hashKey
                ])->one();

                if (!$hQuote) {
                    $nameArray = [];
                    foreach ($rooms as $room) {
                        $totalAmount += $room['amount'];
                        $nameArray[] = $room['code'] ?? '';
                    }

                    $productQuoteDto = new HotelProductQuoteCreateDto();
                    $productQuoteDto->productId = $hotelRequest->ph_product_id;
                    $productQuoteDto->originCurrency = $currency;
                    $productQuoteDto->ownerUserId = $ownerId;
                    $productQuoteDto->clientCurrency = ProductQuoteHelper::getClientCurrencyCode($hotelRequest->phProduct);
                    $productQuoteDto->clientCurrencyRate = ProductQuoteHelper::getClientCurrencyRate($hotelRequest->phProduct);
                    $productQuoteDto->originCurrencyRate = Currency::getBaseRateByCurrencyCode($currency);
                    $productQuoteDto->name = mb_substr(implode(' & ', $nameArray), 0, 40);
                    $productQuoteDto->orderId = $orderId;

                    $productTypeServiceFee = null;
                    $productType = ProductType::find()->select(['pt_service_fee_percent'])->byHotel()->asArray()->one();
                    if ($productType && $productType['pt_service_fee_percent']) {
                        $productTypeServiceFee = $productType['pt_service_fee_percent'];
                    }

                    $prQuote = ProductQuote::create($productQuoteDto, $productTypeServiceFee);

                    if ($prQuote->save()) {
                        $hQuote = new self();
                        $hQuote->hq_hash_key = $hashKey;
                        $hQuote->hq_hotel_id = $hotelRequest->ph_id;
                        $hQuote->hq_hotel_list_id = $hotelModel->hl_id;
                        $hQuote->hq_product_quote_id = $prQuote->pq_id;
                        $hQuote->hq_hotel_name = $hotelModel->hl_name;
                        $hQuote->hq_destination_name = $hotelModel->hl_destination_name;
                        $hQuote->hq_request_hash = $hotelRequest->ph_request_hash_key;
                        $hQuote->hq_origin_search_data = $quoteData;
                        $hQuote->hq_check_in_date = $hotelRequest->ph_check_in_date;
                        $hQuote->hq_check_out_date = $hotelRequest->ph_check_out_date;

                        if (!$hQuote->save()) {
                            Yii::error(
                                VarDumper::dumpAsString($hQuote->errors),
                                'Model:HotelQuote:findOrCreateByData:HotelQuote:save'
                            );
                        }
                    }
                }
            }

            $hotelQuoteRoomAmount = 0;
            $hotelQuoteRoomSystemMarkup = 0;
            $hotelQuoteRoomAgentMarkup = 0;
            if ($hQuote && !$hQuote->hotelQuoteRooms) {
                $totalSystemPrice = 0;
                $totalServiceFeeSum = 0;
                foreach ($rooms as $room) {
                    $importedHotelRoomIds = [];
                    $importHotelRoomStatus = false;
                    $childrenAges = '';
                    if (array_key_exists('childrenAges', $room) && !empty($room['childrenAges'])) {
                        $childrenAgesArr = explode(',', $room['childrenAges']);
                        sort($childrenAgesArr);
                        $childrenAges = implode(',', $childrenAgesArr);
                    }

                    $qRoom = new HotelQuoteRoom();
                    $qRoom->hqr_hotel_quote_id = $hQuote->hq_id;
                    $qRoom->hqr_adults = $room['adults'] ?? null;
                    $qRoom->hqr_children = $room['children'] ?? null;
                    $qRoom->hqr_children_ages = $childrenAges;
                    $qRoom->hqr_rate_comments_id = $room['rateCommentsId'] ?? null;
                    $qRoom->hqr_type = ($room['type'] == HotelQuoteRoom::TYPE_LIST[HotelQuoteRoom::TYPE_BOOKABLE])
                        ? HotelQuoteRoom::TYPE_BOOKABLE : HotelQuoteRoom::TYPE_RECHECK;

                    $qRoom->hqr_rooms = $room['rooms'] ?? null;
                    $qRoom->hqr_code = $room['code'] ?? null;
                    $qRoom->hqr_room_name = $room['name'] ?? null;
                    $qRoom->hqr_key = $room['key'] ?? null;
                    $qRoom->hqr_class = $room['class'] ?? null;
                    $qRoom->hqr_payment_type = $room['paymentType'] ?? null;
                    $qRoom->hqr_board_code = $room['boardCode'] ?? null;
                    $qRoom->hqr_board_name = $room['boardName'] ?? null;
                    $qRoom->hqr_amount = $room['amount'] - ($room['markup'] ?? 0);
                    $qRoom->hqr_rate_comments = $qRoom->prepareRateComments($room);
                    $qRoom->hqr_currency = $currency;
                    $qRoom->hqr_service_fee_percent = ProductTypePaymentMethodQuery::getDefaultPercentFeeByProductType($hQuote->hqProductQuote->pqProduct->pr_type_id) ?? (self::SERVICE_FEE * 100);
                    $qRoom->hqr_system_mark_up = $room['markup'] ?? null;
                    $qRoom->hqr_agent_mark_up = 0;
                    $serviceFeeSum = (($qRoom->hqr_amount + $qRoom->hqr_system_mark_up) * $qRoom->hqr_service_fee_percent / 100);
                    $totalServiceFeeSum += $serviceFeeSum;
                    $totalSystemPrice += $qRoom->hqr_amount + $serviceFeeSum + $qRoom->hqr_system_mark_up;


                    if (isset($room['cancellationPolicies'][0]['amount'])) {
                        $qRoom->hqr_cancel_amount = $room['cancellationPolicies'][0]['amount'];
                    } else {
                        $qRoom->hqr_cancel_amount = $room['cancellationPolicies']['amount'] ?? null;
                    }
                    if (isset($room['cancellationPolicies']) && $room['cancellationPolicies'][0]['from']) {
                        $qRoom->hqr_cancel_from_dt = date("Y-m-d H:i:s", strtotime($room['cancellationPolicies'][0]['from']));
                    } else {
                        $qRoom->hqr_cancel_from_dt = $room['cancellationPolicies']['from'] ?? null;
                    }

                    if ($qRoom->hqr_amount) {
                        $hotelQuoteRoomAmount += $qRoom->hqr_amount;
                    }
                    if ($qRoom->hqr_system_mark_up) {
                        $hotelQuoteRoomSystemMarkup += $qRoom->hqr_system_mark_up;
                    }
                    if ($qRoom->hqr_agent_mark_up) {
                        $hotelQuoteRoomAgentMarkup += $qRoom->hqr_agent_mark_up;
                    }

                    if (!$qRoom->save()) {
                        Yii::error(
                            VarDumper::dumpAsString($qRoom->errors),
                            'Model:HotelQuote:findOrCreateByData:HotelQuoteRoom:save'
                        );
                    }

                    $hotelRoomsQuery = (new Query())
                        ->select(['hr_id'])
                        ->from(HotelRoom::tableName())
                        ->where(['hr_hotel_id' => $hotelRequest->ph_id]);
                    if (count($importedHotelRoomIds)) {
                        $hotelRoomsQuery->andWhere(['NOT IN', 'hr_id', $importedHotelRoomIds]);
                    }
                    $hotelRooms = $hotelRoomsQuery->all();

                    if (count($hotelRooms)) { // trying to find in hotel_room_pax
                        $hotelRoomPax = new HotelRoomPax();
                        foreach ($hotelRooms as $hotelRoom) {
                            $summaryRoom = $hotelRoomPax->getSummaryByRoom($hotelRoom['hr_id']);
                            if (
                                (int) $summaryRoom['adults'] === (int) $qRoom->hqr_adults &&
                                (int) $summaryRoom['children'] === (int) $qRoom->hqr_children &&
                                isset($summaryRoom['childrenAges']) &&
                                $summaryRoom['childrenAges'] === $childrenAges
                            ) { // port info from hotel_room_pax
                                $hotelRoomPaxes = $hotelRoomPax::find()
                                    ->where(['hrp_hotel_room_id' => $hotelRoom['hr_id']])
                                    ->all();

                                foreach ($hotelRoomPaxes as $pax) {
                                    $hotelQuoteRoomPax = new HotelQuoteRoomPax();
                                    $hotelQuoteRoomPax->hqrp_hotel_quote_room_id = $qRoom->hqr_id;
                                    $hotelQuoteRoomPax->hqrp_type_id = $pax->hrp_type_id;
                                    $hotelQuoteRoomPax->hqrp_age = $pax->hrp_age;
                                    $hotelQuoteRoomPax->hqrp_first_name = $pax->hrp_first_name;
                                    $hotelQuoteRoomPax->hqrp_last_name = $pax->hrp_last_name;
                                    $hotelQuoteRoomPax->hqrp_dob = $pax->hrp_dob;
                                    $hotelQuoteRoomPax->save();
                                }
                                $importedHotelRoomIds[] = $hotelRoom['hr_id'];
                                $importHotelRoomStatus = true;
                            }
                        }
                    }

                    if (!$importHotelRoomStatus) { // if not found in hotel_room_pax
                        if (!empty($qRoom->hqr_adults) && $qRoom->hqr_adults) { // adults
                            for ($i = 0; $i < $qRoom->hqr_adults; $i++) {
                                $hotelQuoteRoomPax = new HotelQuoteRoomPax();
                                $hotelQuoteRoomPax->hqrp_hotel_quote_room_id = $qRoom->hqr_id;
                                $hotelQuoteRoomPax->hqrp_type_id = $hotelQuoteRoomPax::PAX_TYPE_ADL;
                                $hotelQuoteRoomPax->save();
                            }
                        }
                        if (!empty($qRoom->hqr_children) && $qRoom->hqr_children) { // children
                            if (isset($childrenAgesArr) && count($childrenAgesArr) === $qRoom->hqr_children) {  // trying to fill age
                                foreach ($childrenAgesArr as $age) {
                                    $hotelQuoteRoomPax = new HotelQuoteRoomPax();
                                    $hotelQuoteRoomPax->hqrp_hotel_quote_room_id = $qRoom->hqr_id;
                                    $hotelQuoteRoomPax->hqrp_type_id = $hotelQuoteRoomPax::PAX_TYPE_CHD;
                                    $hotelQuoteRoomPax->hqrp_age = intval($age);
                                    $hotelQuoteRoomPax->save();
                                }
                            } else { // without age
                                for ($i = 0; $i < $qRoom->hqr_children; $i++) {
                                    $hotelQuoteRoomPax = new HotelQuoteRoomPax();
                                    $hotelQuoteRoomPax->hqrp_hotel_quote_room_id = $qRoom->hqr_id;
                                    $hotelQuoteRoomPax->hqrp_type_id = $hotelQuoteRoomPax::PAX_TYPE_CHD;
                                    $hotelQuoteRoomPax->save();
                                }
                            }
                        }
                    }
                }

                if (isset($prQuote)) {
//                    $prQuote->pq_origin_price = CurrencyHelper::convertToBaseCurrency((float)$hotelQuoteRoomAmount * $hQuote->getCountDays(), $prQuote->pq_origin_currency_rate);
//                    $prQuote->pq_app_markup = CurrencyHelper::convertToBaseCurrency((float)$hotelQuoteRoomSystemMarkup * $hQuote->getCountDays(), $prQuote->pq_origin_currency_rate);
//                     pq_agent_markup - already in base currency
//                    $prQuote->pq_agent_markup = (float)$hotelQuoteRoomAgentMarkup * $hQuote->getCountDays();

//                    $prQuote->calculateServiceFeeSum();
//                    $prQuote->calculatePrice();
//                    $prQuote->calculateClientPrice();

//                    $systemPrice = ProductQuoteHelper::calcSystemPrice((float)$totalSystemPrice, $prQuote->pq_origin_currency);
//                    $prQuote->setQuotePrice(
//                        (float)$totalAmount,
//                        (float)$systemPrice,
//                        ProductQuoteHelper::roundPrice($systemPrice * $prQuote->pq_client_currency_rate),
//                        ProductQuoteHelper::roundPrice((float)$totalServiceFeeSum)
//                    );

                    $prices = (new HotelQuotePriceCalculator())->calculate($hQuote, $prQuote->pq_origin_currency_rate);
                    $prQuote->updatePrices(
                        $prices['originPrice'],
                        $prices['appMarkup'],
                        $prices['agentMarkup']
                    );
                    if (!$prQuote->save()) {
                        Yii::error([
                            'message' => 'ProductQuote save after calculate prices error',
                            'errors' => $prQuote->getErrors(),
                        ], 'HotelQuoteAdd');
                    }
                }
            }
        }

        return $hQuote;
    }

    /**
     * @return string
     */
    public static function generateGid(): string
    {
        return md5(uniqid('hq', true));
    }

    public function serialize(): array
    {
        return (new HotelQuoteSerializer($this))->getData();
    }

    /**
     * @return bool
     */
    public function isBooked(): bool
    {
        return ($this->hqProductQuote->isBooked() && !empty($this->hq_booking_id));
    }

    /**
     * @return bool
     */
    public function isBookable(): bool
    {
        return (ProductQuoteStatus::isBookable($this->hqProductQuote->pq_status_id) && !$this->isBooked());
    }

    public static function findByProductQuote(int $productQuoteId): ?Quotable
    {
        return self::find()->byProductQuote($productQuoteId)->limit(1)->one();
    }

    public function getId(): int
    {
        return $this->hq_id;
    }

    /**
     * @param string $bookingId
     * @return $this
     */
    public function setBookingId(string $bookingId): self
    {
        $this->hq_booking_id = $bookingId;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getAdults(): ?int
    {
        $result = 0;
        foreach ($this->hotelQuoteRooms as $room) {
            $result += $room->hqr_adults;
        }
        return $result;
    }

    /**
     * @return int|null
     */
    public function getChildren(): ?int
    {
        $result = 0;
        foreach ($this->hotelQuoteRooms as $room) {
            $result += $room->hqr_children;
        }
        return $result;
    }

    /**
     * @return float
     */
    public function getProcessingFee(): float
    {
        $processingFeeAmount = $this->hqProductQuote->pqProduct->prType->getProcessingFeeAmount();
        $result = ($this->getAdults() + $this->getChildren()) * $processingFeeAmount;
        return ProductQuoteHelper::roundPrice($result);
    }

    /**
     * @return bool
     */
    public function saveChanges(): bool
    {
        if (!$result = $this->save()) {
            throw new \RuntimeException($this->getErrorSummary(false)[0]);
        }
        return $result;
    }

    /**
     * @return float
     */
    public function getSystemMarkUp(): float
    {
        $result = 0.00;
        foreach ($this->hotelQuoteRooms as $room) {
            $result += $room->hqr_system_mark_up;
        }
        return $result;
    }

    /**
     * @return float
     */
    public function getAgentMarkUp(): float
    {
        $result = 0.00;
        foreach ($this->hotelQuoteRooms as $room) {
            $result += $room->hqr_agent_mark_up;
        }
        return $result;
    }

    public function getCountDays()
    {
        $countDays = 1;
        if ($this->hq_check_out_date && $this->hq_check_in_date) {
            $date1 = date_create(date('Y-m-d', strtotime($this->hq_check_in_date)));
            $date2 = date_create(date('Y-m-d', strtotime($this->hq_check_out_date)));
            $diff = date_diff($date1, $date2);
            $countDays = $diff->days;
        }
        return $countDays;
    }

    public function getProject(): Project
    {
        if ($project = ArrayHelper::getValue($this, 'hqProductQuote.pqProduct.project')) {
            return $project;
        }
        if ($project = ArrayHelper::getValue($this, 'hqProductQuote.pqProduct.prLead.project')) {
            return $project;
        }
        throw new \DomainException('HotelQuote not related to project');
    }

    public function getLead(): ?Lead
    {
        return ArrayHelper::getValue($this, 'hqProductQuote.pqProduct.prLead');
    }

    public function getClient(): ?Client
    {
        return ArrayHelper::getValue($this, 'hqProductQuote.pqProduct.prLead.client');
    }

    public function getOrder(): ?Order
    {
        return ArrayHelper::getValue($this, 'hqProductQuote.pqOrder');
    }
}

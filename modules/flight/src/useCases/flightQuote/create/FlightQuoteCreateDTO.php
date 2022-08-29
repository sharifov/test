<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\flight\src\useCases\form\ChangeQuoteCreateForm;
use modules\flight\src\useCases\reProtectionQuoteManualCreate\form\ReProtectionQuoteCreateForm;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\src\helpers\FlightQuoteHelper;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethodQuery;
use src\auth\Auth;
use src\helpers\product\ProductQuoteHelper;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteCreateDTO
 * @package modules\flight\src\useCases\flightQuote\create
 */
class FlightQuoteCreateDTO
{
    public $flightId;
    public $sourceId;
    public $productQuoteId;
    public $hashKey;
    public $serviceFeePercent;
    public $recordLocator;
    public $gds;
    public $gdsPcc;
    public $gdsOfferId;
    public $typeId;
    public $cabinClass;
    public $tripTypeId;
    public $mainAirline;
    public $fareType;
    public $createdUserId;
    public $createdExpertId;
    public $createdExpertName;
    public $reservationDump;
    public $pricingInfo;
    public $originSearchData;
    public $lastTicketDate;
    public $requestHash;
    public ?string $expirationDate = null;

    /**
     * FlightQuoteCreateDTO constructor.
     * @param Flight $flight
     * @param ProductQuote $productQuote
     * @param array $quote
     * @param int|null $userId
     */
    public function __construct(Flight $flight, ProductQuote $productQuote, array $quote, ?int $userId)
    {
        $key = $quote['key'] ?? serialize($quote);
        $this->flightId = $flight->fl_id;
        $this->sourceId = null;
        $this->productQuoteId = $productQuote->pq_id;
        $this->hashKey = FlightQuoteHelper::generateHashQuoteKey($key);

        $paymentFee = ProductTypePaymentMethodQuery::getDefaultPercentFeeByProductType($productQuote->pqProduct->pr_type_id);
        if ($paymentFee) {
            $this->serviceFeePercent = $paymentFee;
        } else {
            $productTypeServiceFee = 0;
            $productType = ProductType::find()->select(['pt_service_fee_percent'])->byFlight()->asArray()->one();
            if ($productType && $productType['pt_service_fee_percent']) {
                $productTypeServiceFee = $productType['pt_service_fee_percent'];
            }
            $this->serviceFeePercent = $productTypeServiceFee;
        }

        $this->recordLocator = $quote['recordLocator'] ?? null;
        $this->gds = $quote['gds'] ?? null;
        $this->gdsPcc = $quote['pcc'] ?? null;
        $this->gdsOfferId = $quote['gdsOfferId'] ?? null;
        $this->typeId = $flight->originalQuoteExist() ? FlightQuote::TYPE_ALTERNATIVE : FlightQuote::TYPE_BASE;
        $this->cabinClass = $flight->fl_cabin_class;
        $this->tripTypeId = $flight->fl_trip_type_id;
        $this->mainAirline = $quote['validatingCarrier'] ?? null;
        $this->fareType = isset($quote['fareType']) ? FlightQuote::getFareTypeId($quote['fareType']) : null;
        $this->createdUserId = $userId;
        $this->createdExpertId = null;
        $this->createdExpertName = null;
        $this->reservationDump = $quote['reservationDump'] ?? null;
        $this->pricingInfo = !empty($quote['pricingInfo']) ? json_encode($quote['pricingInfo']) : null;
        $this->originSearchData = json_encode($quote);
        $this->lastTicketDate = $quote['prices']['lastTicketDate'] ?? null;
        $this->requestHash = $flight->fl_request_hash_key;
    }

    public static function fillChangeQuoteManual(
        Flight $flight,
        ProductQuote $productQuote,
        array $quote,
        ?int $userId,
        ChangeQuoteCreateForm $form
    ): FlightQuoteCreateDTO {
        $model = new self($flight, $productQuote, $quote, $userId);
        $model->cabinClass = $form->cabin;
        $model->tripTypeId = $form->tripType;
        $model->expirationDate = $form->expirationDate;
        $model->typeId = FlightQuote::TYPE_REPROTECTION;
        $model->serviceFeePercent = 0;
        return $model;
    }
}

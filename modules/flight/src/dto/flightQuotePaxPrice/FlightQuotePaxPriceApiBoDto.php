<?php

namespace modules\flight\src\dto\flightQuotePaxPrice;

use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\helpers\product\ProductQuoteHelper;
use webapi\src\forms\flight\flights\price\detail\PriceDetailApiForm;

/**
 * Class FlightQuotePaxPriceApiBoDto
 */
class FlightQuotePaxPriceApiBoDto
{
    public $flightQuoteId;
    public $flightPaxCodeId;
    public $fare;
    public $tax;
    public $systemMarkUp;
    public $agentMarkUp;
    public $originFare;
    public $originCurrency;
    public $originTax;
    public $clientCurrency;
    public $clientFare;
    public $clientTax;
    public $cnt;

    /**
     * @param FlightQuote $flightQuote
     * @param ProductQuote $productQuote
     * @param PriceDetailApiForm $priceDetailApiForm
     */
    public function __construct(
        FlightQuote $flightQuote,
        ProductQuote $productQuote,
        PriceDetailApiForm $priceDetailApiForm
    ) {
        $this->flightQuoteId = $flightQuote->fq_id;
        $this->flightPaxCodeId = FlightPax::getPaxId($priceDetailApiForm->paxType);

        $this->originFare = $priceDetailApiForm->fare;
        $this->originTax = $priceDetailApiForm->taxes;
        $this->originCurrency = $priceDetailApiForm->currency;

        $this->fare = ProductQuoteHelper::calcSystemPrice((float) $this->originFare, $priceDetailApiForm->currency);
        $this->tax =  ProductQuoteHelper::calcSystemPrice((float) $this->originTax, $priceDetailApiForm->currency);

        $this->clientFare = ProductQuoteHelper::calcClientPrice($this->fare, $productQuote->pqProduct);
        $this->clientTax = ProductQuoteHelper::calcClientPrice($this->tax, $productQuote->pqProduct);
        $this->clientCurrency = $productQuote->pq_client_currency;

        $this->systemMarkUp = $priceDetailApiForm->taxes - $priceDetailApiForm->baseTaxes;
        $this->agentMarkUp = 0.00;
        $this->cnt = $priceDetailApiForm->tickets;
    }
}

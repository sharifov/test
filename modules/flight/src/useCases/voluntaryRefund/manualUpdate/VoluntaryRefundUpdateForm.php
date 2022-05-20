<?php

namespace modules\flight\src\useCases\voluntaryRefund\manualUpdate;

use common\components\validators\CheckIsNumberValidator;
use common\components\validators\NormalizeDateValidator;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;

/**
 * Class VoluntaryRefundUpdateForm
 * @package modules\flight\src\useCases\voluntaryRefund\manualUpdate
 * @property int $refundId
 * @property string $currency
 * @property string $bookingId
 * @property float $totalProcessingFee
 * @property float $totalAirlinePenalty
 * @property float $totalRefundable
 * @property float $totalPaid
 * @property float $refundCost
 * @property TicketForm[] $tickets
 * @property AuxiliaryOptionForm[] $options
 */
class VoluntaryRefundUpdateForm extends \src\forms\CompositeForm
{
    public $refundId;
    public $bookingId;
    public $currency;
    public $totalPaid;
    public $refundCost;
    public $totalProcessingFee;
    public $totalAirlinePenalty;
    public $totalRefundable;
    public ?string $expirationDate = null;

    public function __construct(ProductQuoteRefund $productQuoteRefund, $config = [])
    {
        $this->refundId = $productQuoteRefund->pqr_id;
        $this->bookingId = $productQuoteRefund->productQuote->flightQuote->getLastBookingId();
        $this->currency = $productQuoteRefund->pqr_client_currency;
        $this->totalPaid = (float)$productQuoteRefund->pqr_client_selling_price;
        $this->refundCost = (float)$productQuoteRefund->pqr_client_refund_cost;
        $this->totalProcessingFee = (float)$productQuoteRefund->pqr_client_processing_fee_amount;
        $this->totalAirlinePenalty = (float)$productQuoteRefund->pqr_client_penalty_amount;
        $this->totalRefundable = (float)$productQuoteRefund->pqr_client_refund_amount;

        $this->tickets = array_map(function ($productQuoteObjectRefund) {
            return new TicketForm($productQuoteObjectRefund);
        }, $productQuoteRefund->productQuoteObjectRefunds);

        $this->options = array_map(function ($productQuoteOptionRefund) {
            return new AuxiliaryOptionForm($productQuoteOptionRefund);
        }, $productQuoteRefund->productQuoteOptionRefunds);

        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    protected function internalForms(): array
    {
        return ['tickets', 'options'];
    }

    public function rules(): array
    {
        return [
            [['totalProcessingFee', 'totalAirlinePenalty', 'totalRefundable', 'totalPaid', 'expirationDate'], 'required'],

            [['totalProcessingFee', 'totalAirlinePenalty', 'totalRefundable', 'totalPaid', 'refundCost'], 'number', 'min' => 0],
            [['totalProcessingFee', 'totalAirlinePenalty', 'totalRefundable', 'totalPaid', 'refundCost'], 'filter', 'filter' => 'floatval'],
            [['totalProcessingFee', 'totalAirlinePenalty', 'totalRefundable', 'totalPaid', 'refundCost'], CheckIsNumberValidator::class, 'allowInt' => true],

            [['currency', 'bookingId'], 'safe'],
            [['refundId'], 'integer'],

            ['tickets', 'safe'],
            ['expirationDate', NormalizeDateValidator::class],
            ['expirationDate', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    /**
     * @param string $date
     * @return void
     */
    public function setExpirationDate(string $date): void
    {
        $this->expirationDate = $date;
    }
}

<?php

namespace modules\flight\src\useCases\voluntaryRefund\manualUpdate;

use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;

/**
 * Class TicketForm
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property int $id
 * @property string $number
 * @property float $airlinePenalty
 * @property float $processingFee
 * @property float $refundable
 * @property float $selling
 * @property bool $refundAllowed
 */
class TicketForm extends \yii\base\Model
{
    public $id;

    public $number;

    public $airlinePenalty;

    public $processingFee;

    public $refundable;

    public $selling;

    public $refundAllowed;

    public function __construct(ProductQuoteObjectRefund $productQuoteObjectRefund, $config = [])
    {
        $this->id = $productQuoteObjectRefund->pqor_id;
        $this->airlinePenalty = (float)$productQuoteObjectRefund->pqor_client_penalty_amount;
        $this->processingFee = (float)$productQuoteObjectRefund->pqor_client_processing_fee_amount;
        $this->refundable = (float)$productQuoteObjectRefund->pqor_client_refund_amount;
        $this->selling = (float)$productQuoteObjectRefund->pqor_client_selling_price;
        $this->number = FlightQuoteTicketRefund::findOne(['fqtr_id' => $productQuoteObjectRefund->pqor_quote_object_id])->fqtr_ticket_number ?? null;
        $this->refundAllowed = $productQuoteObjectRefund->pqor_data_json['refundAllowed'] ?? null;
        parent::__construct($config);
    }


    public function rules(): array
    {
        return [
            [['id', 'airlinePenalty', 'processingFee', 'refundable', 'selling'], 'required'],
            [['id'], 'integer'],
            [['airlinePenalty', 'processingFee', 'refundable', 'selling'], 'number', 'min' => 0],
        ];
    }
}

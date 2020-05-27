<?php
namespace sales\model\saleTicket\useCase\create;


class SaleTicketCreateDTO
{
	public $caseId;
	public $caseSaleId;
	public $ticketNumber;
	public $clientName;
	public $recordLocator;
	public $originalFop;
	public $chargeSystem;
	public $penaltyType;
	public $penaltyAmount;
	public $selling;
	public $serviceFee;
	public $recallCommission;
	public $markup;
	public $transactionIds;

	public function feelBySaleData(int $caseId, int $caseSaleId, string $pnr, string $clientName, int $cntPassengers, array $rule, array $refundRules): self
	{
		$dto = new self();

		$dto->caseId = $caseId;
		$dto->caseSaleId = $caseSaleId;
		$dto->ticketNumber = $rule['ticket_number'] ?? null;
		$dto->clientName = $clientName;
		$dto->recordLocator = $pnr;
		$dto->originalFop = $refundRules['original_FOP'] ?? null;
		$dto->chargeSystem = (string)($refundRules['charge_system'] ?? null);
		$dto->penaltyType = $refundRules['airline_penalty'] ?? null;
		$dto->penaltyAmount = (string)($refundRules['refund_waiver'] ?? 0);
		$dto->selling = $rule['selling_price'] ?? null;
		$dto->serviceFee = $rule['original_service_fee'] ?? null;
		$dto->recallCommission = ($refundRules['recall_commission'] ?? 0) / ($cntPassengers ?: 1);
		$dto->markup = $rule['service_fee_amount'] ?? 0;
		$dto->transactionIds = implode(',', $refundRules['transaction_IDs']);

		return $dto;
	}
}
<?php

namespace modules\product\src\abac\dto;

use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use src\access\EmployeeGroupAccess;
use src\auth\Auth;
use stdClass;

/**
 * Class ProductQuoteRefundDto
 * @package modules\product\src\abac\dto
 *
 * @property int|null $pqrTypeId
 * @property int|null $pqrStatusId
 * @property int|null $csCategoryId
 * @property bool $isCaseOwner
 * @property bool $isCommonGroup
 * @property int|null $csStatusId
 * @property bool $isAutomateCase
 * @property int|null $csProjectId
 * @property bool $hasPqcInvoluntaryActive
 */
class ProductQuoteRefundAbacDto extends stdClass
{
    public ?int $pqrTypeId = null;
    public ?int $pqrStatusId = null;

    public ?int $csCategoryId = null;
    public bool $isCaseOwner = false;
    public bool $isCommonGroup = false;
    public ?int $csStatusId = null;
    public bool $isAutomateCase;
    public ?int $csProjectId = null;

    public ?int $pqStatusId;
    public bool $isPqChangeable;
    public bool $isOwner;
    public bool $hasPqrActive;
    public bool $hasPqcActive;
    public bool $hasPqcAccepted;
    public bool $hasPqcInvoluntaryActive;

    public int $prTypeId;
    public ?int $prProjectId;

    public ?int $orProjectId = null;
    public ?int $orStatusId = null;
    public ?int $orPayStatusId = null;
    public bool $isOrderOwner;
    public ?int $orTypeId = null;

    public function __construct(?ProductQuoteRefund $productQuoteRefund)
    {
        $userId = Auth::id();

        if ($productQuoteRefund) {
            $this->pqrTypeId = $productQuoteRefund->pqr_type_id;
            $this->pqrStatusId = $productQuoteRefund->pqr_status_id;

            $this->csCategoryId = $productQuoteRefund->case->cs_category_id;
            $this->isCaseOwner = $productQuoteRefund->case->isOwner($userId);
            if ($productQuoteRefund->case->hasOwner()) {
                $this->isCommonGroup = EmployeeGroupAccess::isUserInCommonGroup($userId, $productQuoteRefund->case->cs_user_id);
            }
            $this->csStatusId = $productQuoteRefund->case->cs_status;
            $this->isAutomateCase = $productQuoteRefund->case->isAutomate();
            $this->csProjectId = $productQuoteRefund->case->cs_project_id;

            $this->pqStatusId = $productQuoteRefund->productQuote->pq_status_id;
            $this->isPqChangeable = $productQuoteRefund->productQuote->isChangeable();
            $this->isOwner = $productQuoteRefund->productQuote->isOwner($userId);
            $this->hasPqrActive = (bool)$productQuoteRefund->productQuote->productQuoteRefundsActive;
            $this->hasPqcActive = (bool)$productQuoteRefund->productQuote->productQuoteChangesActive;
            $this->hasPqcAccepted = $productQuoteRefund->productQuote->isProductQuoteChangeAccepted();
            $this->hasPqcInvoluntaryActive = (bool)$productQuoteRefund->productQuote->productQuoteInvoluntaryChangesActive;

            $this->prTypeId = $productQuoteRefund->productQuote->pqProduct->pr_type_id;
            $this->prProjectId = $productQuoteRefund->productQuote->pqProduct->pr_project_id;

            $this->orProjectId = $productQuoteRefund->productQuote->pqOrder->or_project_id;
            $this->orStatusId = $productQuoteRefund->productQuote->pqOrder->or_status_id;
            $this->orPayStatusId = $productQuoteRefund->productQuote->pqOrder->or_pay_status_id;
            $this->isOrderOwner = $productQuoteRefund->productQuote->pqOrder->isOwner($userId);
            $this->orTypeId  = $productQuoteRefund->productQuote->pqOrder->or_type_id;
        }
    }
}

<?php

namespace modules\product\src\abac\dto;

use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\service\ProductQuoteChangeService;
use src\access\EmployeeGroupAccess;
use src\auth\Auth;
use src\entities\cases\Cases;

/**
 * Class ProductQuoteAbacDto
 * @package modules\product\src\abac\dto
 *
 * @property bool $is_new
 * @property int|null $pqStatusId
 * @property bool $isPqChangeable
 * @property bool $isOwner
 * @property bool $hasPqrActive
 * @property bool $hasPqcActive
 * @property bool $hasPqrAccepted;
 * @property bool $hasPqcAccepted;
 * @property bool $hasPqcInvoluntaryActive
 * @property int $prTypeId
 * @property int|null $prProjectId
 * @property int|null $orProjectId
 * @property int|null $orStatusId
 * @property int|null $orPayStatusId
 * @property bool $isOrderOwner
 * @property int|null $orTypeId
 * @property int|null $csCategoryId
 * @property bool $isCaseOwner
 * @property bool $isCommonGroup
 * @property int|null $csStatusId
 * @property bool $isAutomateCase
 * @property int|null $csProjectId
 */
class ProductQuoteAbacDto extends \stdClass
{
    public bool $is_new;
    public ?int $pqStatusId;
    public bool $isPqChangeable;
    public bool $isOwner;
    public bool $hasPqrActive;
    public bool $hasPqcActive;
    public bool $hasPqrAccepted;
    public bool $hasPqcAccepted;
    public bool $hasPqcInvoluntaryActive;
    public int $prTypeId;
    public ?int $prProjectId;
    public ?int $orProjectId = null;
    public ?int $orStatusId = null;
    public ?int $orPayStatusId = null;
    public bool $isOrderOwner;
    public ?int $orTypeId = null;
    public ?int $csCategoryId = null;
    public bool $isCaseOwner = false;
    public bool $isCommonGroup = false;
    public ?int $csStatusId = null;
    public bool $isAutomateCase;
    public ?int $csProjectId = null;
    public int $userId;

    public function __construct(?ProductQuote $productQuote)
    {
        $this->userId = Auth::id();

        if ($productQuote) {
            $this->is_new = $productQuote->isNew();
            $this->pqStatusId = $productQuote->pq_status_id;
            $this->isPqChangeable = $productQuote->isChangeable();
            $this->isOwner = $productQuote->isOwner($this->userId);
            $this->hasPqrActive = (bool)$productQuote->productQuoteRefundsActive;
            $this->hasPqcActive = (bool)$productQuote->productQuoteChangesActive;
            $this->hasPqrAccepted = $productQuote->isProductQuoteRefundAccepted();
            $this->hasPqcAccepted = $productQuote->isProductQuoteChangeAccepted();
            $this->hasPqcInvoluntaryActive = (bool)$productQuote->productQuoteInvoluntaryChangesActive;

            $this->prTypeId = $productQuote->pqProduct->pr_type_id;
            $this->prProjectId = $productQuote->pqProduct->pr_project_id;
        }
    }

    public function mapOrderAttributes(?Order $order)
    {
        if ($order) {
            $this->orProjectId = $order->or_project_id;
            $this->orStatusId = $order->or_status_id;
            $this->orPayStatusId = $order->or_pay_status_id;
            $this->isOrderOwner = $order->isOwner($this->userId);
            $this->orTypeId  = $order->or_type_id;
        }
    }

    public function mapCaseAttributes(?Cases $case)
    {
        if ($case) {
            $this->csCategoryId = $case->cs_category_id;
            $this->isCaseOwner = $case->isOwner($this->userId);

            if ($case->hasOwner()) {
                $this->isCommonGroup = EmployeeGroupAccess::isUserInCommonGroup($this->userId, $case->cs_user_id);
            }

            $this->csStatusId = $case->cs_status;
            $this->isAutomateCase = $case->isAutomate();
            $this->csProjectId = $case->cs_project_id;
        }
    }
}

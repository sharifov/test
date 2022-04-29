<?php

namespace modules\product\src\abac\dto;

use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use src\access\EmployeeGroupAccess;
use src\auth\Auth;

/**
 * Class ProductQuoteChangeAbacDto
 * @package modules\product\src\abac\dto
 *
 * @property int|null $pqcTypeId
 * @property int|null $pqcStatusId
 * @property bool $isAutomatePqc
 * @property int|null $pqcDecisionId
 * @property int|null $csCategoryId
 * @property bool $isCaseOwner
 * @property bool $isCommonGroup
 * @property int|null $csStatusId
 * @property bool $isAutomateCase
 * @property int|null $csProjectId
 * @property int|null $pqStatusId
 * @property bool $isPqChangeable
 * @property bool $isOwner
 * @property bool $hasPqrActive
 * @property bool $hasPqcActive
 * @property bool $hasPqcInvoluntaryActive
 * @property int $prTypeId
 * @property int|null $prProjectId
 * @property int|null $orProjectId
 * @property int|null $orStatusId
 * @property int|null $orPayStatusId
 * @property bool $isOrderOwner
 * @property int|null $orTypeId
 * @property bool|null $refundAllowed
 */
class ProductQuoteChangeAbacDto extends \stdClass
{
    public ?int $pqcTypeId = null;
    public ?int $pqcStatusId = null;
    public bool $isAutomatePqc = false;
    public ?int $pqcDecisionId = null;

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
    public bool $hasPqrAccepted;
    public bool $hasPqcInvoluntaryActive;
    public int $prTypeId;
    public ?int $prProjectId;

    public ?int $orProjectId = null;
    public ?int $orStatusId = null;
    public ?int $orPayStatusId = null;
    public bool $isOrderOwner;
    public ?int $orTypeId = null;
    public ?int $maxConfirmableQuotesCnt = null;
    public ?bool $refundAllowed = true;

    public function __construct(?ProductQuoteChange $productQuoteChange)
    {
        if ($productQuoteChange) {
            $userId = Auth::id();

            $this->pqcTypeId = $productQuoteChange->pqc_type_id;
            $this->pqcStatusId = $productQuoteChange->pqc_status_id;
            $this->isAutomatePqc = $productQuoteChange->isAutomate();
            $this->pqcDecisionId = $productQuoteChange->pqc_decision_type_id;
            $this->csCategoryId = $productQuoteChange->pqcCase->cs_category_id;
            $this->isCaseOwner = $productQuoteChange->pqcCase->isOwner($userId);

            if ($productQuoteChange->pqcCase->hasOwner()) {
                $this->isCommonGroup = EmployeeGroupAccess::isUserInCommonGroup($userId, $productQuoteChange->pqcCase->cs_user_id);
            }

            $this->csStatusId = $productQuoteChange->pqcCase->cs_status;
            $this->isAutomateCase = $productQuoteChange->pqcCase->isAutomate();
            $this->csProjectId = $productQuoteChange->pqcCase->cs_project_id;

            $this->pqStatusId = $productQuoteChange->pqcPq->pq_status_id;
            $this->isPqChangeable = $productQuoteChange->pqcPq->isChangeable();
            $this->isOwner = $productQuoteChange->pqcPq->isOwner($userId);
            $this->hasPqrActive = (bool)$productQuoteChange->pqcPq->productQuoteRefundsActive;
            $this->hasPqcActive = (bool)$productQuoteChange->pqcPq->productQuoteChangesActive;
            $this->hasPqrAccepted = $productQuoteChange->pqcPq->isProductQuoteRefundAccepted();
            $this->hasPqcInvoluntaryActive = (bool)$productQuoteChange->pqcPq->productQuoteInvoluntaryChangesActive;

            $this->prTypeId = $productQuoteChange->pqcPq->pqProduct->pr_type_id;
            $this->prProjectId = $productQuoteChange->pqcPq->pqProduct->pr_project_id;

            $this->orProjectId = $productQuoteChange->pqcPq->pqOrder->or_project_id;
            $this->orStatusId = $productQuoteChange->pqcPq->pqOrder->or_status_id;
            $this->orPayStatusId = $productQuoteChange->pqcPq->pqOrder->or_pay_status_id;
            $this->isOrderOwner = $productQuoteChange->pqcPq->pqOrder->isOwner($userId);
            $this->orTypeId  = $productQuoteChange->pqcPq->pqOrder->or_type_id;
            $this->refundAllowed = $productQuoteChange->pqc_refund_allowed;
        }
    }
}

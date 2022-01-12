<?php

namespace modules\product\src\abac\dto;

use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use src\access\EmployeeGroupAccess;
use src\auth\Auth;
use src\entities\cases\Cases;
use stdClass;

/**
 * Class ProductQuoteAbacDto
 * @package modules\product\src\abac\dto
 *
 * @property int|null $relPqStatusId
 * @property bool $relPqIsOwner
 * @property bool $relPqIsChangeable
 * @property bool $relPqHasPqrActive
 * @property bool $relPqHasPqcActive
 * @property bool $relPqIsRecommended
 * @property int|null $relationType
 * @property int|null $parPqStatusId
 * @property bool $parPqIsOwner
 * @property bool $parPqIsChangeable
 * @property bool $parPqHasPqrActive
 * @property bool $parPqHasPqcActive
 * @property int|null $pqcTypeId
 * @property int|null $pqcStatusId
 * @property bool $isAutomatePqc
 * @property int|null $pqcDecisionId
 */

class RelatedProductQuoteAbacDto extends stdClass
{
    public ?int $relPqStatusId;
    public bool $relPqIsOwner;
    public bool $relPqIsChangeable;
    public bool $relPqHasPqrActive;
    public bool $relPqHasPqcActive;
    public bool $relPqIsRecommended;
    public int $relationType;
    public ?int $parPqStatusId;
    public bool $parPqIsOwner;
    public bool $parPqIsChangeable;
    public bool $parPqHasPqrActive;
    public bool $parPqHasPqcActive;
    public bool $parHasPqcInvoluntaryActive;
    public int $parPrTypeId;
    public ?int $parPrProjectId;
    public ?int $orProjectId = null;
    public ?int $orStatusId = null;
    public ?int $orPayStatusId = null;
    public ?bool $isOrderOwner = false;
    public ?int $orTypeId = null;
    public int $userId;
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
    public ?bool $pqcRefundAllowed = true;

    public function __construct(?ProductQuote $relatedPrQt)
    {
        $this->userId = Auth::id();

        if ($relatedPrQt) {
            $this->relPqStatusId = $relatedPrQt->pq_status_id;
            $this->relPqIsOwner = $relatedPrQt->isOwner($this->userId);
            $this->relPqIsChangeable = $relatedPrQt->isChangeable();
            $this->relPqHasPqrActive = (bool)$relatedPrQt->productQuoteRefundsActive;
            $this->relPqHasPqcActive = (bool)$relatedPrQt->productQuoteChangesActive;
            $this->relPqIsRecommended = $relatedPrQt->isRecommended();

            $this->relationType = $relatedPrQt->pqRelation->pqr_type_id;

            $parentPrQt = $relatedPrQt->relateParent;
            $this->parPqStatusId = $parentPrQt->pq_status_id;
            $this->parPqIsOwner = $parentPrQt->isOwner($this->userId);
            $this->parPqIsChangeable = $parentPrQt->isChangeable();
            $this->parPqHasPqrActive = (bool)$parentPrQt->productQuoteRefundsActive;
            $this->parPqHasPqcActive = (bool)$parentPrQt->productQuoteChangesActive;
            $this->parHasPqcInvoluntaryActive = (bool)$parentPrQt->productQuoteInvoluntaryChangesActive;

            $this->parPrTypeId = $parentPrQt->pqProduct->pr_type_id;
            $this->parPrProjectId = $parentPrQt->pqProduct->pr_project_id;
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

    public function mapProductQuoteChangeAttributes(?ProductQuoteChange $productQuoteChange)
    {
        if ($productQuoteChange) {
            $this->pqcTypeId = $productQuoteChange->pqc_type_id;
            $this->pqcStatusId = $productQuoteChange->pqc_status_id;
            $this->isAutomatePqc = $productQuoteChange->isAutomate();
            $this->pqcDecisionId = $productQuoteChange->pqc_decision_type_id;
            $this->pqcRefundAllowed = $productQuoteChange->pqc_refund_allowed;
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

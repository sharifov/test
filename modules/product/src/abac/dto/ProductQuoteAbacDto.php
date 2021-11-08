<?php

namespace modules\product\src\abac\dto;

use modules\product\src\entities\productQuote\ProductQuote;
use sales\access\EmployeeGroupAccess;
use sales\auth\Auth;

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

    public function __construct(?ProductQuote $productQuote)
    {
        if ($productQuote) {
            $userId = Auth::id();

            $this->is_new = $productQuote->isNew();
            $this->pqStatusId = $productQuote->pq_status_id;
            $this->isPqChangeable = $productQuote->isChangeable();
            $this->isOwner = $productQuote->isOwner($userId);
            $this->hasPqrActive = (bool)$productQuote->productQuoteRefundsActive;
            $this->hasPqcActive = (bool)$productQuote->productQuoteChangesActive;

            if ($case = $productQuote->pqOrder->caseOrder[0]->cases) {
                $this->csCategoryId = $case->cs_category_id;
                $this->isCaseOwner = $case->isOwner($userId);

                if ($case->hasOwner()) {
                    $this->isCommonGroup = EmployeeGroupAccess::isUserInCommonGroup($userId, $case->cs_user_id);
                }

                $this->csStatusId = $case->cs_status;
                $this->isAutomateCase = $case->isAutomate();
                $this->csProjectId = $case->cs_project_id;
            }

            $this->prTypeId = $productQuote->pqProduct->pr_type_id;
            $this->prProjectId = $productQuote->pqProduct->pr_project_id;

            if ($productQuote->pqOrder) {
                $this->orProjectId = $productQuote->pqOrder->or_project_id;
                $this->orStatusId = $productQuote->pqOrder->or_status_id;
                $this->orPayStatusId = $productQuote->pqOrder->or_pay_status_id;
                $this->isOrderOwner = $productQuote->pqOrder->isOwner($userId);
                $this->orTypeId  = $productQuote->pqOrder->or_type_id;
            }
        }
    }
}

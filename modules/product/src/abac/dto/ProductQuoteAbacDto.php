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
 * @property bool $is_common_group
 * @property bool $isAutomateCase
 * @property int|null $csProjectId
 */
class ProductQuoteAbacDto extends \stdClass
{
    public bool $is_new;
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
    public bool $isCaseOwner;
    public bool $isCommonGroup = false;
    public ?int $csStatusId = null;
    public bool $isAutomateCase;
    public ?int $csProjectId = null;

    public function __construct(?ProductQuote $productQuote)
    {
        if ($productQuote) {
            $userId = Auth::id();

            $this->is_new = $productQuote->isNew();
            $this->isPqChangeable = $productQuote->isChangeable();
            $this->isOwner = $productQuote->isOwner($userId);
            $this->hasPqrActive = (bool)$productQuote->productQuoteRefundsActive;
            $this->hasPqcActive = (bool)$productQuote->productQuoteChangesActive;

            if ($productQuote->productQuoteLastChange) {
                $this->csCategoryId = $productQuote->productQuoteLastChange->pqcCase->cs_category_id;
                $this->isCaseOwner = $productQuote->productQuoteLastChange->pqcCase->isOwner($userId);

                if ($productQuote->productQuoteLastChange->pqcCase->hasOwner()) {
                    $this->isCommonGroup = EmployeeGroupAccess::isUserInCommonGroup($userId, $productQuote->productQuoteLastChange->pqcCase->cs_user_id);
                }

                $this->isAutomateCase = $productQuote->productQuoteLastChange->pqcCase->isAutomate();
                $this->csProjectId = $productQuote->productQuoteLastChange->pqcCase->cs_project_id;
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

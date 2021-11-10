<?php

namespace modules\product\src\abac\dto;

use modules\order\src\entities\order\Order;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\auth\Auth;
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
    public int $parPrTypeId;
    public ?int $parPrProjectId;
    public ?int $orProjectId;
    public ?int $orStatusId;
    public ?int $orPayStatusId;
    public ?bool $isOrderOwner = false;
    public ?int $orTypeId;
    public int $userId;

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
            $this->parPrTypeId = $parentPrQt->pqProduct->pr_type_id;
            $this->parPrProjectId = $parentPrQt->pqProduct->pr_project_id;

            /*$this->orProjectId = $parentPrQt->pqOrder->or_project_id;
            $this->orStatusId = $parentPrQt->pqOrder->or_status_id;
            $this->orPayStatusId = $parentPrQt->pqOrder->or_pay_status_id;
            $this->isOrderOwner = $parentPrQt->pqOrder->isOwner($userId);
            $this->orTypeId  = $parentPrQt->pqOrder->or_type_id;*/
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
}

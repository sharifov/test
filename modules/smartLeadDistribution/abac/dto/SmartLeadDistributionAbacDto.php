<?php

namespace modules\smartLeadDistribution\abac\dto;

use src\repositories\lead\LeadBadgesRepository;

class SmartLeadDistributionAbacDto extends \stdClass
{
    public int $quantity_first_category = 0;
    public int $quantity_second_category = 0;
    public int $quantity_third_category = 0;

    public function __construct()
    {
        $leadBadgeRepository = new LeadBadgesRepository();
        $catAmountList = $leadBadgeRepository->countBusinessLeadsByRatingCategory();

        $this->quantity_first_category = $catAmountList[1] ?? 0;
        $this->quantity_second_category = $catAmountList[2] ?? 0;
        $this->quantity_third_category = $catAmountList[3] ?? 0;
    }
}

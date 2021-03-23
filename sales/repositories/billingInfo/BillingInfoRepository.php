<?php

namespace sales\repositories\billingInfo;

use common\models\BillingInfo;

class BillingInfoRepository
{
    public function save(BillingInfo $billingInfo): void
    {
        if (!$billingInfo->save(false)) {
            throw new \RuntimeException($billingInfo->getErrorSummary(true)[0]);
        }
    }
}

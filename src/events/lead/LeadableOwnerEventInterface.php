<?php

namespace src\events\lead;

use common\models\Lead;

interface LeadableOwnerEventInterface
{
    public function getLead(): Lead;
    public function getOldOwnerId(): ?int;
    public function getNewOwnerId(): ?int;
}

<?php

namespace src\events\lead;

use common\models\Lead;

interface LeadableEventInterface
{
    public function getLead(): Lead;
}

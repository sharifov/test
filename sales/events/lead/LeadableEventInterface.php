<?php

namespace sales\events\lead;

use common\models\Lead;

interface LeadableEventInterface
{
    public function getLead(): Lead;
}

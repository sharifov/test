<?php

namespace sales\model\leadOrder\entity;

class LeadOrderRepository
{
    public function save(LeadOrder $leadOrder): void
    {
        if (!$leadOrder->save(false)) {
            throw new \RuntimeException('Saving error');
        }
    }
}

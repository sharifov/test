<?php

namespace sales\model\leadProduct\entity;

class LeadProductRepository
{
    public function save(LeadProduct $leadProduct): void
    {
        if (!$leadProduct->save(false)) {
            throw new \RuntimeException('Saving error');
        }
    }
}

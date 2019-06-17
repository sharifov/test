<?php

namespace sales\repositories\lead;

use common\models\Lead;
use sales\repositories\NotFoundException;

class LeadRepository
{
    public function get($id): Lead
    {
        if ($lead = Lead::findOne($id)) {
            return $lead;
        }
        throw new NotFoundException('Lead is not found');
    }

    public function getByGid($gid): Lead
    {
        if ($lead = Lead::findOne(['gid' => $gid])) {
            return $lead;
        }
        throw new NotFoundException('Lead is not found');
    }

    public function save(Lead $lead): int
    {
        if ($lead->save(false)) {
            return $lead->id;
        }
        throw new \RuntimeException('Saving error');
    }

    public function updateOnlyTripType(Lead $lead): void
    {
        if (!$lead->updateAttributes(['trip_type'])) {
            throw new \RuntimeException('Update trip type error');
        }
    }

    public function remove(Lead $lead): void
    {
        if (!$lead->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}
<?php

namespace sales\repositories\lead;

use common\models\Lead;
use sales\repositories\NotFoundException;

class LeadRepository
{
    public function get($id) : Lead
    {
        if (!$lead = Lead::findOne($id)) {
            throw new NotFoundException('Lead is not found.');
        }
        return $lead;
    }

    public function getByGid($gid) : Lead
    {
        if (!$lead = Lead::findOne(['gid' => $gid])) {
            throw new NotFoundException('Lead is not found.');
        }
        return $lead;
    }

    public function save(Lead $lead) : void
    {
        if (!$lead->save(false)) {
            throw new \RuntimeException('Saving error.');
        }
    }

    public function remove(Lead $lead) : void
    {
        if (!$lead->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }
}
<?php

namespace modules\objectSegment\src\object\dto;

use common\models\Client;
use modules\lead\src\entities\lead\LeadQuery;
use modules\objectSegment\src\contracts\ObjectSegmentDtoInterface;

class ClientSegmentObjectDto implements ObjectSegmentDtoInterface
{
    public int $client_id;

    public ?int $count_sold_leads = null;

    public function __construct(Client $client)
    {
        $this->client_id = $client->id;
        $this->count_sold_leads = LeadQuery::countSoldLeadsByClient($client->id);
    }

    public function getEntityId(): int
    {
        return $this->client_id;
    }
}

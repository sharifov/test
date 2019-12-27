<?php

namespace webapi\src\response\lead;

use webapi\src\response\DataResponse;

class LeadCreateResponse extends DataResponse
{
    protected $key = 'lead';

    public $uid;
    public $gid;
}

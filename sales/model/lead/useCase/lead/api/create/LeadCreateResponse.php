<?php

namespace sales\model\lead\useCase\lead\api\create;

use webapi\src\response\DataResponse;

class LeadCreateResponse extends DataResponse
{
    protected $key = 'lead';

    public $id;
    public $uid;
    public $gid;
}

<?php

namespace sales\model\lead\useCase\lead\api\create;

use webapi\src\response\messages\MessageValue;

/**
 * Class LeadCreateValue
 *
 * @property $id
 * @property $uid
 * @property $gid
 */
class LeadCreateValue extends MessageValue
{
    public $id;
    public $uid;
    public $gid;
}

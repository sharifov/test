<?php

namespace src\model\lead\useCases\lead\api\create;

use webapi\src\response\messages\MessageValue;

/**
 * Class LeadCreateValue
 *
 * @property $id
 * @property $uid
 * @property $gid
 * @property $client_id
 * @property $leadDataInserted
 * @property $experiments
 * @property $clientDataInserted
 * @property $warnings
 */
class LeadCreateValue extends MessageValue
{
    public $id;
    public $uid;
    public $gid;
    public $client_id;
    public $client;
    public $leadDataInserted = [];
    public $experiments = [];
    public $clientDataInserted = [];
    public $warnings = [];
}

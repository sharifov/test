<?php

namespace modules\email\src\abac\dto;

use common\models\Email;

class EmailAbacDto extends \stdClass
{
    public ?bool $is_owner_new = true;
    public function __construct(?Email $email)
    {
    }
}

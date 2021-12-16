<?php

namespace modules\qaTask\src\abac\dto;

use modules\qaTask\src\entities\qaTask\QaTask;

class QaTaskAbacDto extends \stdClass
{
    public function __construct(?QaTask $qaTask)
    {
        if ($qaTask) {
            // TODO: Add additional conditions or attributes is necessary
        }
    }
}

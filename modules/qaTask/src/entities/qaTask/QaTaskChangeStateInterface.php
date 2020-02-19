<?php

namespace modules\qaTask\src\entities\qaTask;

use modules\qaTask\src\entities\qaTaskStatusLog\CreateDto;

interface QaTaskChangeStateInterface
{
    public function getChangeStateLog(): CreateDto;
}

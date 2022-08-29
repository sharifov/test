<?php

namespace modules\userStats\src\abac\dto;

use stdClass;

/**
 * Class UserStatsDto
 * @package modules\userStats\src\abac\dto
 */
class UserStatsDto extends stdClass
{
    public int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }
}

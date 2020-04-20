<?php

namespace common\components\mail;

/**
 * Class Result
 *
 * @property int|null $lastEmailId
 * @property array $projectsIds
 */
class Result
{
    public $lastEmailId;
    public $projectsIds;

    public function __construct(?int $lastEmailId = null, array $projectsIds = [])
    {
        $this->lastEmailId = $lastEmailId;
        $this->projectsIds = $projectsIds;
    }
}

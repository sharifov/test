<?php

namespace modules\email\src;

/**
 * Class Projects
 *
 * @property $projects
 */
class Projects
{
    private $projects;

    /**
     * @param array $projects ex.
     [
        'test.com' => ['id' => 1, 'postfix' => 'test.com'],
        'next.com' => ['id' => 1, 'postfix' => 'next.com'],
     ]
     */
    public function __construct(array $projects)
    {
        $this->projects = $projects;
    }

    public function getProjectId(?string $emailTo): ?int
    {
        $emailProjectArr = explode('@', $emailTo);
        $projectId = null;
        if (count($emailProjectArr) && isset($emailProjectArr[1])) {
            $emailToHost = $emailProjectArr[1];
            if (array_key_exists($emailToHost, $this->projects)) {
                $projectId = $this->projects[$emailToHost]['id'];
            }
        }
        return $projectId;
    }
}

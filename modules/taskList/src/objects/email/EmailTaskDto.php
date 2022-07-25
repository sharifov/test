<?php

namespace modules\taskList\src\objects\email;

use src\entities\email\EmailInterface;

class EmailTaskDto extends \stdClass
{
    public ?string $project_key = null;

    public function __construct(EmailInterface $email)
    {
        $this->project_key = $email->project->project_key ?? null;
    }
}

<?php

namespace modules\taskList\src\objects\email;

use src\entities\email\EmailInterface;

class EmailTaskDto extends \stdClass
{
    public ?string $project_key = null;
    public ?string $template_type_key = null;
    public ?bool $email_has_client = null;

    public function __construct(EmailInterface $email)
    {
        $this->project_key = $email->project->project_key ?? null;
        $this->template_type_key = $email->templateType->etp_key ?? null;
        $this->email_has_client = (bool) $email->hasClient();
    }
}

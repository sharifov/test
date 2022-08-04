<?php

namespace modules\taskList\src\objects\email;

use common\models\Email;
use yii\helpers\ArrayHelper;

class EmailTaskDto extends \stdClass
{
    public ?string $project_key = null;
    public ?string $template_type_key = null;
    public ?bool $email_has_client = null;

    public function __construct(Email $email)
    {
        $this->project_key = $email->eProject->project_key ?? null;
        $this->template_type_key = $email->eTemplateType->etp_key ?? null;
        $this->email_has_client = (bool) $email->e_client_id;
    }
}

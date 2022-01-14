<?php

namespace modules\email\src\abac\dto;

use common\models\Lead;
use src\entities\cases\Cases;

/**
 * @property int|null $template_id
 */
class EmailPreviewDto extends \stdClass
{
    public ?int $template_id;
    public ?bool $is_message_edited;
    public ?bool $is_subject_edited;
    public ?int $project_id;
    public ?int $department_id;

    public function __construct(
        ?int $emailTemplateId,
        ?bool $messageEdited,
        ?bool $subjectEdited,
        ?Lead $lead,
        ?Cases $case
    ) {
        $this->template_id = $emailTemplateId;

        $this->is_message_edited = $messageEdited;
        $this->is_subject_edited = $subjectEdited;

        if ($lead) {
            $this->project_id = $lead->project_id;
            $this->department_id = $lead->l_dep_id;
        } else if ($case && $case->cs_project_id) {
            $this->project_id = $case->cs_project_id;
            $this->department_id = $case->cs_dep_id;
        }
    }
}

<?php

namespace modules\product\src\entities\product\dto;

/**
 * Class CreateDto
 *
 * @property int $pr_lead_id
 * @property int $pr_type_id
 * @property string|null $pr_name
 * @property string|null $pr_description
 */
class CreateDto
{
    public $pr_lead_id;
    public $pr_type_id;
    public $pr_name;
    public $pr_description;

    public function __construct(int $pr_lead_id, int $pr_type_id, ?string $pr_name, ?string $pr_description)
    {
        $this->pr_lead_id = $pr_lead_id;
        $this->pr_type_id = $pr_type_id;
        $this->pr_name = $pr_name;
        $this->pr_description = $pr_description;
    }
}

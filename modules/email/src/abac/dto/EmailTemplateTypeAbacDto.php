<?php

namespace modules\email\src\abac\dto;

class EmailTemplateTypeAbacDto extends \stdClass
{
    public ?string $template_key;

    public function __construct(
        ?string $template_key
    ) {
        $this->template_key = $template_key;
    }
}

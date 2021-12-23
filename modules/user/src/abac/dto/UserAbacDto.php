<?php

namespace modules\user\src\abac\dto;

use stdClass;

class UserAbacDto extends stdClass
{
    public string $formAttribute = '';
    public array $formMultiAttribute = [];
    public ?bool $isNewRecord = null;

    public function __construct(?string $attributeName = null)
    {
        if ($attributeName) {
            $this->formAttribute = $attributeName;
            $this->formMultiAttribute[0] = $attributeName;
        }
    }
}

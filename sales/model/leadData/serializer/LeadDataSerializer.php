<?php

namespace sales\model\leadData\serializer;

use sales\entities\serializer\Serializer;

/**
 * Class LeadDataSerializer
 */
class LeadDataSerializer extends Serializer
{
    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'ld_field_key',
            'ld_field_value',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->toArray();
    }
}

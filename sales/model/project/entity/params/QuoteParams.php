<?php

namespace sales\model\project\entity\params;

/**
 * Class QuoteParams
 * @package sales\model\project\entity\params
 *
 * @property bool $enableRandomProjectProviderId
 */
class QuoteParams
{
    public bool $enableRandomProjectProviderId = false;

    public function __construct(array $params)
    {
        $this->enableRandomProjectProviderId = (bool)($params['enable_random_project_provider_id'] ?? false);
    }

    public static function default(): array
    {
        return [
            'enableRandomProjectProviderId' => false
        ];
    }
}

<?php

namespace modules\featureFlag\models\debug;

/**
 * @property string|null $project_key
 * @property int|null $department_id
 * @property string|null $app_type
 */
class DebugFeatureFlagDTO
{
    public ?string $project_key;
    public ?int $department_id;
    public ?string $app_type;
}

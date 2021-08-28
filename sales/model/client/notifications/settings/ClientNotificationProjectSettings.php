<?php

namespace sales\model\client\notifications\settings;

use sales\model\project\entity\params\ClientNotificationObject;

interface ClientNotificationProjectSettings
{
    public function getNotificationSettings(int $projectId, string $type): ?ClientNotificationObject;
}

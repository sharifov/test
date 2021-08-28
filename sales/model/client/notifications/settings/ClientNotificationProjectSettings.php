<?php

namespace sales\model\client\notifications\settings;

use common\models\Project;
use sales\model\project\entity\params\ClientNotificationObject;

/**
 * Class ClientNotificationProjectSettings
 *
 * @property array $settings
 */
class ClientNotificationProjectSettings
{
    public function getNotificationSettings(int $projectId, string $type): ?ClientNotificationObject
    {
        $project = Project::find()->byId($projectId)->one();

        if (!$project) {
            return null;
        }

        if (!$project->getParams()->clientNotification->typeExist($type)) {
            return null;
        }

        $typeNotification = $project->getParams()->clientNotification->$type;
        if ($typeNotification instanceof ClientNotificationObject) {
            return $typeNotification;
        }

        return null;
    }
}

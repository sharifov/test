<?php

namespace sales\model\clientChat\entity\projectConfig\service;

use common\models\Language;
use sales\helpers\app\AppHelper;
use sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig;

class ClientChatProjectConfigService
{
    /**
     * Returns an array with keys that have not been removed
     *
     * @param int $projectId
     * @return array
     */
    public function deleteConfigCacheActiveLanguages(int $projectId): array
    {
        $keyCacheNotDeleted = [];

        $languages = Language::getLanguages();

        foreach ($languages as $languageId => $languageName) {
            $keyCache = ClientChatProjectConfig::getCacheKey($projectId, (string)$languageId);

            if (!\Yii::$app->webApiCache->delete($keyCache)) {
                $keyCacheNotDeleted[] = $keyCache;
            }
        }
        return $keyCacheNotDeleted;
    }
}

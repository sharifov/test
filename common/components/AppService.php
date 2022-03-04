<?php

/**
 * Created
 * User: alex.connor@techork.com
 * Date: 2022-03-02
 * Time: 11:05 AM
 */

namespace common\components;

use frontend\helpers\JsonHelper;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class AppService
 */

class AppService
{
    /**
     * @param string|null $dir
     * @return array
     */
    public static function getComposerLockData(?string $dir = null): array
    {
        if (empty($dir)) {
            $dir = Yii::getAlias('@root');
        }
        $composerJson = file_get_contents($dir . DIRECTORY_SEPARATOR . 'composer.lock');
        $dependencies = JsonHelper::decode($composerJson, true);
        $export = [];
        if (!empty($dependencies['packages'])) {
            foreach ($dependencies['packages'] as $dependency) {
                $export[] = [
                    'name' => $dependency['name'],
                    'type' => $dependency['type'],
                    'version' => $dependency['version'],
                    'license' => implode(', ', $dependency['license'] ?? []),
                    'source' => $dependency['source']['url'] ?? $dependency['dist']['url'] ?? '',
                    'authors' => implode(', ', ArrayHelper::getColumn($dependency['authors'] ?? [], 'name')),
                    'comments' => $dependency['description'] ?? '',
                ];
            }
        }
        return $export;
    }
}

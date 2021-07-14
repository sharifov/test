<?php

namespace sales\helpers\app;

use common\components\Metrics;
use Yii;
use yii\caching\TagDependency;

/**
 * Class ReleaseVersionHelper
 */
class ReleaseVersionHelper
{
    public static function getReleaseVersion(bool $regToMetrics = false): ?string
    {
        $releaseVersion = Yii::$app->params['release']['version'];
        if ($releaseVersion && $regToMetrics) {
            self::registerToMetric($releaseVersion);
        }
        return $releaseVersion;
    }

    public static function registerToMetric(string $releaseVersion): void
    {
        $keyTag = 'release-version-' . $releaseVersion . '-' . php_uname('n');
        Yii::$app->cache->getOrSet($keyTag, function () use ($releaseVersion) {
            /** @var Metrics $metrics */
            $metrics = \Yii::$container->get(Metrics::class);
            $metrics->gaugeMetric(
                'release_version',
                '',
                1,
                ['version' => $releaseVersion]
            );
            return $releaseVersion;
        }, 60 * 60 * 24, new TagDependency(['tags' => 'release-version']));
    }
}

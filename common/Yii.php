<?php

/**
 * Created by PhpStorm.
 * User: alexandr
 * Date: 12/5/18
 * Time: 4:30 PM
 */


/**
 * Yii bootstrap file.
 * Used for enhanced IDE code autocompletion.
 */
class Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static $app;
}

/**
 * Class BaseApplication
 * Used for properties that are identical for both WebApplication and ConsoleApplication
 *
 */
abstract class BaseApplication extends yii\base\Application
{
}


/**
 * Class WebApplication
 * Include only Web application related components here
 *
 * @property \aki\telegram\Telegram $telegram The Telegram component. This property is read-only. Extended component.
 * @property \common\components\CommunicationService $communication The CommunicationService component. This property is read-only. Extended component.
 * @property \common\components\HybridService $hybrid The HybridService component. This property is read-only. Extended component.
 * @property \common\components\CurrencyService $currency The CurrencyService component.
 * @property \common\components\AirSearchService $airsearch The AirSearchService component.
 * @property \yii\queue\beanstalk\Queue $queue_job The beanstalk Queue. This property is read-only. Extended component.
 * @property \yii\queue\beanstalk\Queue $queue_email_job The beanstalk Queue. This property is read-only. Extended component.
 * @property \yii\queue\beanstalk\Queue $queue_phone_check The beanstalk Queue. This property is read-only. Extended component.
 * @property \yii\queue\beanstalk\Queue $queue_client_chat_job The beanstalk Queue. This property is read-only. Extended component.
 * @property \yii\queue\beanstalk\Queue $queue_lead_redial The beanstalk Queue. This property is read-only. Extended component.
 * @property \yii\caching\Cache $cacheFile FileCache.
 * @property \yii\caching\Cache $webApiCache FileCache.
 * @property \yii\caching\Cache $consoleCache FileCache.
 * @property \yii\redis\Connection $redis Redis Connection.
 * @property \common\components\RocketChat $rchat Rocket Chat component
 * @property \common\components\ChatBot $chatBot ChatBot component
 * @property \common\components\TravelServices $travelServices TravelServices component
 * @property \common\components\ga\GaRequestService $gaRequestService GaRequestService component
 * @property \kivork\PrometheusClient\components\PrometheusClient $prometheus Prometheus client component
 * @property \sorokinmedia\centrifugo\Client $centrifugo Centrifugo client component
 * @property \modules\abac\components\AbacComponent $abac ABAC component
 * @property \modules\objectSegment\components\ObjectSegmentComponent $objectSegment Object Segment component
 * @property \common\components\SnowplowService $snowplow Snowplow component
 * @property \common\components\antispam\CallAntiSpamService $callAntiSpam CallAntiSpamService component
 * @property \yii\i18n\Formatter $formatter_search Search Formatter component w/o timezone
 * @property \yii\authclient\Collection $authClientCollection Auth Client Collection
 * @property \kivork\FeatureFlag\components\FeatureFlagComponent $ff FeatureFlag component
 * @property \modules\eventManager\components\EventManagerComponent $event Event component
 *
 *
 *
 */
class WebApplication extends yii\web\Application
{
}

/**
 * Class ConsoleApplication
 * Include only Console application related components here
 *
 */
class ConsoleApplication extends yii\console\Application
{
}

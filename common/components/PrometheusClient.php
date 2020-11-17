<?php
namespace common\components;

use http\Exception\RuntimeException;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use yii\base\Component;

/**
 * Class PrometheusClient
 * @package common\components
 *
 * @property bool $useHttpBasicAuth
 * @property string $authUsername
 * @property string $authPassword
 * @property bool $enabled
 * @property CollectorRegistry $registry
 * @property array $redisOptions
 */
class PrometheusClient extends Component
{
    public string $authUsername;
    public string $authPassword;

    public bool $useHttpBasicAuth = true;
    public bool $enabled = true;

    private CollectorRegistry $registry;

    public array $redisOptions = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => null,
        'timeout' => 0.1, // in seconds
        'read_timeout' => '10', // in seconds
        'persistent_connections' => false
    ];


    public function init() : void
    {
        parent::init();
        if ($this->enabled) {
            try {
                if ($this->redisOptions) {
                    \Prometheus\Storage\Redis::setDefaultOptions($this->redisOptions);
                }

                $this->registry = CollectorRegistry::getDefault();
            } catch (\Throwable $throwable) {
                \Yii::error($throwable, 'PrometheusClient:init:Throwable');
            }
        }
    }


    /**
     * @return string
     */
    public function getMetric(): string
    {
        if ($this->enabled) {
            $registry = \Prometheus\CollectorRegistry::getDefault();
            $renderer = new RenderTextFormat();
            return $renderer->render($registry->getMetricFamilySamples());
        } else {
            return 'Prometheus Client is disabled';
        }
    }
}

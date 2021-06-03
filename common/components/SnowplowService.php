<?php

namespace common\components;

use Snowplow\Tracker\Emitters\SyncEmitter;
use Snowplow\Tracker\Subject;
use Snowplow\Tracker\Tracker;
use yii\helpers\VarDumper;

/**
 * Class SnowplowService
 * @package common\components\snowplow
 *
 * @version 0.1.0
 *
 * @property string $collectorUrl
 * @property string $appId
 * @property bool $enabled
 * @property bool $debug
 * @property bool $base64EncodeEnabled
 */
class SnowplowService extends \yii\base\Component
{
    public string $collectorUrl;
    public string $appId;
    public bool $enabled = false;
    public bool $debug = false;
    public bool $base64EncodeEnabled = true;

    /**
     * @param string $category
     * @param string $action
     * @param array $data
     */
    public function trackAction(string $category, string $action, array $data = []): void
    {
        if (!$this->enabled) {
            return;
        }

        $emitter = new SyncEmitter($this->collectorUrl, 'http', 'POST', 1, $this->debug);
        $subject = new Subject();
        $tracker = new Tracker($emitter, $subject, null, $this->appId, $this->base64EncodeEnabled);

        $tracker->trackStructEvent($category, $action, null, null, null, $data, time());

        try {
            if ($this->debug) {
                /** @var SyncEmitter $emitter */
                $emitter = $tracker->returnEmitters()[0];
                $results = $emitter->returnRequestResults();

                \Yii::info(VarDumper::dumpAsString($results), 'info\trackStructEvent');

                if ((int)$results[0]['code'] !== 200) {
                    \Yii::error('Sending data to snowplow failed: ' . VarDumper::dumpAsString($results), 'SnowplowService::trackAction');
                }
            }
        } catch (\Throwable $e) {
            \Yii::error('Parsing of sent data to snowplow failed, reason: ' . $e->getMessage(), 'SnowplowService::trackAction::Throwable');
        }
    }
}

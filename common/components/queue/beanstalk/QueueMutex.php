<?php

namespace common\components\queue\beanstalk;

use yii\di\Instance;
use yii\mutex\Mutex;

/**
 * Class Queue
 *
 * @property Mutex $mutex
 */
class QueueMutex extends \yii\queue\beanstalk\Queue
{
    /**
     * @var Mutex|string|array the Mutex object or the application component ID of the Mutex.
     * This can also be an array that is used to create a mutex instance in case you do not want do configure
     * mutex as an application component.
     */
    public $mutex = 'mutex';

    public function init()
    {
        parent::init();
        $this->mutex = Instance::ensure($this->mutex, Mutex::class);
    }

    public function run($repeat, $timeout = 0)
    {
        return $this->runWorker(function (callable $canContinue) use ($repeat, $timeout) {
            while ($canContinue()) {
                if ($this->mutex->acquire($this->tube)) {
                    if ($payload = $this->getPheanstalk()->reserveFromTube($this->tube, $timeout)) {
                        $info = $this->getPheanstalk()->statsJob($payload);
                        if (
                            $this->handleMessage(
                                $payload->getId(),
                                $payload->getData(),
                                $info->ttr,
                                $info->reserves
                            )
                        ) {
                            $this->getPheanstalk()->delete($payload);
                        }
                    } elseif (!$repeat) {
                        $this->mutex->release($this->tube);
                        break;
                    }
                    $this->mutex->release($this->tube);
                }
            }
        });
    }
}

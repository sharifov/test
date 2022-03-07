<?php

namespace common\components\queue\beanstalk;

use yii\di\Instance;
use yii\redis\Mutex;

/**
 * Class Queue
 *
 * @property Mutex $mutex
 * @property int $delayAfterReleaseMutex
 */
class QueueMutex extends \yii\queue\beanstalk\Queue
{
    public $mutex = 'mutex';
    public $delayAfterReleaseMutex = 100;

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
                        $this->delayAfterRelease();
                        break;
                    }
                    $this->mutex->release($this->tube);
                    $this->delayAfterRelease();
                }
            }
        });
    }

    private function delayAfterRelease(): void
    {
        usleep(($this->mutex->retryDelay + $this->delayAfterReleaseMutex) * 1000);
    }
}

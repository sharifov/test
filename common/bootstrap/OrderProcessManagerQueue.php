<?php

namespace common\bootstrap;

use modules\order\src\processManager\queue\DummyQueue;
use modules\order\src\processManager\queue\Queue;
use modules\order\src\processManager\queue\SimpleQueue;
use yii\base\BootstrapInterface;

class OrderProcessManagerQueue implements BootstrapInterface
{
    public function bootstrap($app)
    {
        $container = \Yii::$container;
        $container->setSingleton(Queue::class, static function () {
            return new SimpleQueue(\Yii::$app->queue_job);
//            return new DummyQueue();
        });
    }
}

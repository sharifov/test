<?php

namespace common\bootstrap;

use sales\model\leadRedial\queue\LeadRedialQueue;
use sales\model\leadRedial\queue\NullLeadRedialQueue;
use sales\model\leadRedial\queue\SimpleLeadRedialQueue;
use sales\model\leadRedial\queue\TestLeadRedialQueue;
use yii\base\BootstrapInterface;

class LeadRedialSetUp implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = \Yii::$container;

        $container->setSingleton(LeadRedialQueue::class, static function () use ($container) {
//            return new TestLeadRedialQueue();
            return new NullLeadRedialQueue();
//            return $container->get(SimpleLeadRedialQueue::class);
        });
    }
}

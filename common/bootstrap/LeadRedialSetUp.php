<?php

namespace common\bootstrap;

use sales\model\leadRedial\queue\CallNextLeads;
use sales\model\leadRedial\queue\LeadRedialQueue;
use sales\model\leadRedial\queue\Leads;
use sales\model\leadRedial\queue\NullLeadRedialQueue;
use sales\model\leadRedial\queue\SimpleLeadRedialQueue;
use sales\model\leadRedial\queue\TestLeadRedialQueue;
use sales\model\leadRedial\queue\TestLeads;
use yii\base\BootstrapInterface;

class LeadRedialSetUp implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = \Yii::$container;

        $container->setSingleton(LeadRedialQueue::class, static function () use ($container) {
//            return new TestLeadRedialQueue();
//            return new NullLeadRedialQueue();
            return $container->get(SimpleLeadRedialQueue::class);
        });

        $container->setSingleton(Leads::class, static function () use ($container) {
            return new CallNextLeads();
//            return new TestLeads();
        });
    }
}

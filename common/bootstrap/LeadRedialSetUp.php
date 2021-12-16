<?php

namespace common\bootstrap;

use sales\helpers\setting\SettingHelper;
use sales\model\leadRedial\assign\EmployeeUsers;
use sales\model\leadRedial\assign\TestUsers;
use sales\model\leadRedial\assign\Users;
use sales\model\leadRedial\priorityLevel\PriorityLevelCalculator;
use sales\model\leadRedial\priorityLevel\SettingsPriorityLevelCalculator;
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

        $container->setSingleton(Users::class, static function () use ($container) {
            return new EmployeeUsers();
//            return new TestUsers();
        });

        $container->setSingleton(PriorityLevelCalculator::class, static function () use ($container) {
            return new SettingsPriorityLevelCalculator();
        });
    }
}

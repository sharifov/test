<?php

namespace common\bootstrap;

use src\helpers\setting\SettingHelper;
use src\model\leadRedial\assign\EmployeeUsers;
use src\model\leadRedial\assign\TestUsers;
use src\model\leadRedial\assign\Users;
use src\model\leadRedial\priorityLevel\PriorityLevelCalculator;
use src\model\leadRedial\priorityLevel\SettingsPriorityLevelCalculator;
use src\model\leadRedial\queue\CallNextLeads;
use src\model\leadRedial\queue\LeadRedialQueue;
use src\model\leadRedial\queue\Leads;
use src\model\leadRedial\queue\NullLeadRedialQueue;
use src\model\leadRedial\queue\SimpleLeadRedialQueue;
use src\model\leadRedial\queue\TestLeadRedialQueue;
use src\model\leadRedial\queue\TestLeads;
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

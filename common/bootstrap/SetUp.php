<?php

namespace common\bootstrap;

use sales\dispatchers\DeferredEventDispatcher;
use sales\dispatchers\EventDispatcher;
use sales\dispatchers\SimpleEventDispatcher;
use sales\events\lead\LeadBookedEvent;
use sales\events\lead\LeadCallExpertRequestEvent;
use sales\events\lead\LeadCreatedCloneEvent;
use sales\events\lead\LeadCreatedEvent;
use sales\events\lead\LeadDuplicateDetectedEvent;
use sales\events\lead\LeadFollowUpEvent;
use sales\events\lead\LeadOwnerChangedEvent;
use sales\events\lead\LeadCountPassengersChangedEvent;
use sales\events\lead\LeadSnoozeEvent;
use sales\events\lead\LeadSoldEvent;
use sales\events\lead\LeadStatusChangedEvent;
use sales\events\lead\LeadTaskEvent;
use sales\listeners\lead\LeadBookedEventListener;
use sales\listeners\lead\LeadCallExpertRequestEventListener;
use sales\listeners\lead\LeadCreatedCloneEventListener;
use sales\listeners\lead\LeadCreatedEventListener;
use sales\listeners\lead\LeadDuplicateDetectedEventListener;
use sales\listeners\lead\LeadFlowListener;
use sales\listeners\lead\LeadFollowUpEventListener;
use sales\listeners\lead\LeadOwnerChangedEventListener;
use sales\listeners\lead\LeadCountPassengersChangedEventListener;
use sales\listeners\lead\LeadSnoozeEventListener;
use sales\listeners\lead\LeadSoldEventListener;
use sales\listeners\lead\LeadTaskEventListener;
use yii\base\BootstrapInterface;
use yii\di\Container;

class SetUp implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $container = \Yii::$container;

        $container->setSingleton(EventDispatcher::class, DeferredEventDispatcher::class);

        $container->setSingleton(DeferredEventDispatcher::class, function (Container $container) {
            return new DeferredEventDispatcher(new SimpleEventDispatcher($container, [
                LeadCreatedEvent::class => [LeadCreatedEventListener::class],
                LeadStatusChangedEvent::class => [LeadFlowListener::class],
                LeadDuplicateDetectedEvent::class => [LeadDuplicateDetectedEventListener::class],
                LeadSoldEvent::class => [LeadSoldEventListener::class],
                LeadOwnerChangedEvent::class => [LeadOwnerChangedEventListener::class],
                LeadBookedEvent::class => [LeadBookedEventListener::class],
                LeadFollowUpEvent::class => [LeadFollowUpEventListener::class],
                LeadSnoozeEvent::class => [LeadSnoozeEventListener::class],
                LeadCallExpertRequestEvent::class => [LeadCallExpertRequestEventListener::class],
                LeadTaskEvent::class => [LeadTaskEventListener::class],
                LeadCountPassengersChangedEvent::class => [LeadCountPassengersChangedEventListener::class],
                LeadCreatedCloneEvent::class => [LeadCreatedCloneEventListener::class],
            ]));
        });
    }

}
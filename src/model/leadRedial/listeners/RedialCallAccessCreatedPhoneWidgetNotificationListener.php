<?php

namespace src\model\leadRedial\listeners;

use common\models\Department;
use common\models\Lead;
use common\models\Notifications;
use src\model\leadRedial\entity\events\CallRedialAccessCreatedEvent;
use src\model\voip\phoneDevice\device\ReadyVoipDevice;

class RedialCallAccessCreatedPhoneWidgetNotificationListener
{
    public function handle(CallRedialAccessCreatedEvent $event)
    {
        $lead = Lead::findOne($event->leadId);
        if (!$lead) {
            return;
        }
        Notifications::publish(
            'addPriorityCall',
            ['user_id' => $event->userId],
            [
                'data' => [
                    'command' => 'addPriorityCall',
                    'project' => $lead->project_id ? $lead->project->name : '',
                    'department' => $lead->l_dep_id ? Department::getName($lead->l_dep_id) : '',
                ]
            ]
        );
    }
}

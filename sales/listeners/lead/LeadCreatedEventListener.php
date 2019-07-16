<?php

namespace sales\listeners\lead;

use common\models\LeadFlow;
use common\models\LeadFlowChecklist;
use sales\events\lead\LeadStatusChangedEvent;
use sales\repositories\lead\LeadChecklistRepository;
use sales\repositories\lead\LeadFlowChecklistRepository;
use sales\repositories\lead\LeadFlowRepository;
use webapi\models\ApiUser;
use yii\console\Application;
use Yii;

/**
 * Class LeadStatusChangedEventListener
 * @property LeadFlowRepository $leadFlowRepository
 * @property LeadFlowChecklistRepository $leadFlowChecklistRepository
 * @property LeadChecklistRepository $leadChecklistRepository
 */
class LeadStatusChangedEventListener
{
    private $leadFlowRepository;
    private $leadFlowChecklistRepository;
    private $leadChecklistRepository;

    public function __construct(
        LeadFlowRepository $leadFlowRepository,
        LeadFlowChecklistRepository $leadFlowChecklistRepository,
        LeadChecklistRepository $leadChecklistRepository
    )
    {
        $this->leadFlowRepository = $leadFlowRepository;
        $this->leadFlowChecklistRepository = $leadFlowChecklistRepository;
        $this->leadChecklistRepository = $leadChecklistRepository;
    }

    public function handle(LeadStatusChangedEvent $event): void
    {

        if ($preview = $this->leadFlowRepository->getPreview($event->lead->id)) {
            $preview->setEndedTime();
            try {
                $this->leadFlowRepository->save($preview);
            } catch (\Exception $e) {
                Yii::error($e->getMessage(), 'LeadStatusChangedEventListener:leadFlow:preview:save');
                $preview = null;
            }
        } else {
            $preview = null;
        }

        $current = LeadFlow::create(
            $event->lead->id,
            ($preview && $preview->status) ? $preview->status : $event->oldStatus,
            $event->newStatus,
            (!is_a(Yii::$app, Application::class) && !Yii::$app->user->isGuest && Yii::$app->user->identityClass !== ApiUser::class) ? Yii::$app->user->id : null,
            $event->lead->status_description ? mb_substr($event->lead->status_description, 0, 250) : null
        );

        try {
            $this->leadFlowRepository->save($current);
            if ($event->employeeId && $checkLists = $this->leadChecklistRepository->get($event->employeeId, $event->lead->id)) {
                foreach ($checkLists as $checkList) {
                    $leadFlowChecklist = LeadFlowChecklist::create($current->id, $checkList->lc_type_id, $checkList->lc_user_id);
                    try {
                        $this->leadFlowChecklistRepository->save($leadFlowChecklist);
                    } catch (\Exception $e) {
                        Yii::error($e->getMessage(), 'LeadStatusChangedEventListener:leadFlowChecklist:save');
                    }
                }
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), 'LeadStatusChangedEventListener:leadFlow:current:save');
        }
    }

}
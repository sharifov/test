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
 * Class LeadFlowListener
 * @property LeadFlowRepository $leadFlowRepository
 * @property LeadFlowChecklistRepository $leadFlowChecklistRepository
 * @property LeadChecklistRepository $leadChecklistRepository
 */
class LeadFlowListener
{
    private $leadFlowRepository;
    private $leadFlowChecklistRepository;
    private $leadChecklistRepository;

    /**
     * LeadFlowListener constructor.
     * @param LeadFlowRepository $leadFlowRepository
     * @param LeadFlowChecklistRepository $leadFlowChecklistRepository
     * @param LeadChecklistRepository $leadChecklistRepository
     */
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

    /**
     * @param LeadStatusChangedEvent $event
     */
    public function handle(LeadStatusChangedEvent $event): void
    {
        $lead = $event->lead;

        if ($preview = $this->leadFlowRepository->getPreview($lead->id)) {
            $preview->setEndedTime();
            try {
                $this->leadFlowRepository->save($preview);
            } catch (\Exception $e) {
                Yii::error($e->getMessage(), static::class . ':leadFlow:preview:save');
                $preview = null;
            }
        } else {
            $preview = null;
        }

        $current = LeadFlow::create(
            $lead->id,
            ($preview && $preview->status) ? $preview->status : $event->oldStatus,
            $event->newStatus,
            (!is_a(Yii::$app, Application::class) && !Yii::$app->user->isGuest && Yii::$app->user->identityClass !== ApiUser::class) ? Yii::$app->user->id : null,
            $lead->status_description ? mb_substr($lead->status_description, 0, 250) : null
        );

        try {
            $this->leadFlowRepository->save($current);
            if ($event->ownerId && $checkLists = $this->leadChecklistRepository->get($event->ownerId, $lead->id)) {
                foreach ($checkLists as $checkList) {
                    $leadFlowChecklist = LeadFlowChecklist::create($current->id, $checkList->lc_type_id, $checkList->lc_user_id);
                    try {
                        $this->leadFlowChecklistRepository->save($leadFlowChecklist);
                    } catch (\Exception $e) {
                        Yii::error($e->getMessage(), static::class .  ':leadFlowChecklist:save');
                    }
                }
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), static::class . ':leadFlow:current:save');
        }
    }

}
<?php

namespace sales\services\lead;

use sales\repositories\lead\LeadChecklistRepository;
use sales\repositories\lead\LeadFlowChecklistRepository;
use Yii;
use common\models\LeadFlow;
use common\models\LeadFlowChecklist;
use sales\repositories\lead\LeadFlowRepository;

/**
 * Class LeadFlowLogService
 *
 * @property LeadFlowRepository $leadFlowRepository
 * @property LeadChecklistRepository $leadChecklistRepository
 * @property LeadFlowChecklistRepository $leadFlowChecklistRepository
 */
class LeadFlowLogService
{

    private $leadFlowRepository;
    private $leadChecklistRepository;
    private $leadFlowChecklistRepository;

    /**
     * @param LeadFlowRepository $leadFlowRepository
     * @param LeadChecklistRepository $leadChecklistRepository
     * @param LeadFlowChecklistRepository $leadFlowChecklistRepository
     */
    public function __construct(
        LeadFlowRepository $leadFlowRepository,
        LeadChecklistRepository $leadChecklistRepository,
        LeadFlowChecklistRepository $leadFlowChecklistRepository
    )
    {
        $this->leadFlowRepository = $leadFlowRepository;
        $this->leadChecklistRepository = $leadChecklistRepository;
        $this->leadFlowChecklistRepository = $leadFlowChecklistRepository;
    }

    /**
     * @param int $leadId
     * @param int $newStatus
     * @param int|null $oldStatus
     * @param int|null $ownerId
     * @param int|null $createdUserId
     * @param string|null $description
     * @param string|null $created
     */
    public function log(int $leadId,
                        int $newStatus,
                        ?int $oldStatus = null,
                        ?int $ownerId = null,
                        ?int $createdUserId = null,
                        ?string $description = '',
                        ?string $created = ''): void
    {
        if ($previous = $this->leadFlowRepository->getPrevious($leadId)) {
            $previous->end($created);
            $this->leadFlowRepository->save($previous);
        }
        $current = LeadFlow::create(
            $leadId,
            $newStatus,
            $oldStatus,
            $ownerId,
            $createdUserId,
            $description,
            $created
        );
        $this->leadFlowRepository->save($current);

        if ($oldStatus !== $newStatus) {
            if ($ownerId && $checkLists = $this->leadChecklistRepository->getAll($ownerId, $leadId)) {
                foreach ($checkLists as $checkList) {
                    $leadFlowChecklist = LeadFlowChecklist::create($current->id, $checkList->lc_type_id, $checkList->lc_user_id);
                    try {
                        $this->leadFlowChecklistRepository->save($leadFlowChecklist);
                    } catch (\Exception $e) {
                        Yii::error($e->getMessage(), 'LeadFlowLogService:leadFlowChecklist:save');
                    }
                }
            }
        }

    }

}

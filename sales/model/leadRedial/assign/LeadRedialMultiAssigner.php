<?php

namespace sales\model\leadRedial\assign;

use common\models\Lead;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\leadRedial\entity\CallRedialUserAccessRepository;

/**
 * Class LeadRedialMultiAssigner
 *
 * @property Users $users
 * @property CallRedialUserAccessRepository $callRedialUserAccessRepository
 * @property LeadRedialAssigner $leadRedialAssigner
 */
class LeadRedialMultiAssigner
{
    private Users $users;
    private CallRedialUserAccessRepository $callRedialUserAccessRepository;
    private LeadRedialAssigner $leadRedialAssigner;

    public function __construct(
        Users $users,
        CallRedialUserAccessRepository $callRedialUserAccessRepository,
        LeadRedialAssigner $leadRedialAssigner
    ) {
        $this->users = $users;
        $this->callRedialUserAccessRepository = $callRedialUserAccessRepository;
        $this->leadRedialAssigner = $leadRedialAssigner;
    }

    public function assign(Lead $lead, int $limitUsers, \DateTimeImmutable $createdDt): bool
    {
        $enabledSortingForBusinessLead = $lead->isBusiness() && SettingHelper::getRedialBusinessFlightLeadsMinimumSkillLevel();

        $users = $this->users->getUsers($lead, $limitUsers, $enabledSortingForBusinessLead);

        $countAgentsWithMinimumSkillValue = 0;
        $isAssigned = false;
        foreach ($users as $user) {
            try {
                if ($enabledSortingForBusinessLead) {
                    if ($countAgentsWithMinimumSkillValue && (int)$user['up_skill'] < SettingHelper::getRedialBusinessFlightLeadsMinimumSkillLevel()) {
                        continue;
                    }

                    $this->leadRedialAssigner->assign($lead->id, $user['id'], $createdDt);

                    if ((int)$user['up_skill'] >= SettingHelper::getRedialBusinessFlightLeadsMinimumSkillLevel()) {
                        $countAgentsWithMinimumSkillValue++;
                    }
                } else {
                    $this->leadRedialAssigner->assign($lead->id, $user['id'], $createdDt);
                }
                $isAssigned = true;
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Lead redial assign user error',
                    'leadId' => $lead->id,
                    'userId' => $user['id'],
                    'exception' => AppHelper::throwableLog($e, false),
                ], 'LeadRedialMultiAssigner:assign:Throwable');
            }
        }
        return $isAssigned;
    }
}

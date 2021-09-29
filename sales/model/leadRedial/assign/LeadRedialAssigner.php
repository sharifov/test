<?php

namespace sales\model\leadRedial\assign;

use sales\model\leadRedial\entity\CallRedialUserAccess;
use sales\model\leadRedial\entity\CallRedialUserAccessRepository;

/**
 * Class LeadRedialAssigner
 *
 * @property Users $users
 * @property CallRedialUserAccessRepository $callRedialUserAccessRepository
 */
class LeadRedialAssigner
{
    private Users $users;
    private CallRedialUserAccessRepository $callRedialUserAccessRepository;

    public function __construct(
        Users $users,
        CallRedialUserAccessRepository $callRedialUserAccessRepository
    ) {
        $this->users = $users;
        $this->callRedialUserAccessRepository = $callRedialUserAccessRepository;
    }

    public function assign(int $leadId, int $countUsers, \DateTimeImmutable $createdDt): void
    {
        $users = $this->users->getUsers($leadId, $countUsers);

        foreach ($users as $userId) {
            try {
                $access = CallRedialUserAccess::create($leadId, $userId, $createdDt);
                $this->callRedialUserAccessRepository->save($access);
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => $e->getMessage(),
                    'userId' => $userId,
                    'leadId' => $leadId,
                ], 'NewLeadRedialAssigner');
            }
        }
    }

    public function remove(int $leadId, int $userId): void
    {
        $access = $this->callRedialUserAccessRepository->find($leadId, $userId);
        $this->callRedialUserAccessRepository->remove($access);
    }
}

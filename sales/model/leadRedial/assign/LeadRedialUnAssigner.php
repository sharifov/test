<?php

namespace sales\model\leadRedial\assign;

use sales\model\leadRedial\entity\CallRedialUserAccess;
use sales\model\leadRedial\entity\CallRedialUserAccessRepository;

/**
 * Class LeadRedialAssigner
 *
 * @property CallRedialUserAccessRepository $callRedialUserAccessRepository
 */
class LeadRedialUnAssigner
{
    private CallRedialUserAccessRepository $callRedialUserAccessRepository;

    public function __construct(CallRedialUserAccessRepository $callRedialUserAccessRepository)
    {
        $this->callRedialUserAccessRepository = $callRedialUserAccessRepository;
    }

    public function unAssign(int $userId): void
    {
        $accesses = CallRedialUserAccess::find()->andWhere(['crua_user_id' => $userId])->all();
        foreach ($accesses as $access) {
            try {
                $this->callRedialUserAccessRepository->remove($access);
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Cant remove Call Redial User Access',
                    'exception' => $e->getMessage(),
                ], 'LeadRedialUnAssigner');
            }
        }
    }
}

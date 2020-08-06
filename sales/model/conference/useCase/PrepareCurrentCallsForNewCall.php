<?php

namespace sales\model\conference\useCase;

/**
 * Class PrepareCurrentCallsForNewCall
 *
 */
class PrepareCurrentCallsForNewCall
{
    public function prepare(int $userId): bool
    {
        if ($disconnect = (new DisconnectFromAllActiveClientsCreatedConferences())->disconnect($userId)) {
            if ($completeU2U = (new CompleteAllUserToUserCalls())->complete($userId)) {
                if ($completeJoin = (new CompleteAllJoinCalls())->complete($userId)) {
                    if ($completeSimple = (new CompleteAllSimpleCalls())->complete($userId)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}

<?php

namespace sales\model\leadRedial\assign;

use sales\model\leadRedial\entity\CallRedialUserAccess;

class LeadRedialAccessChecker
{
    public function exist(int $userId): bool
    {
        return CallRedialUserAccess::find()->byUserId($userId)->withoutExpired()->exists();
    }
}

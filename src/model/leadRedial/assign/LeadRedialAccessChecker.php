<?php

namespace src\model\leadRedial\assign;

use src\model\leadRedial\entity\CallRedialUserAccess;

class LeadRedialAccessChecker
{
    public function exist(int $userId): bool
    {
        return CallRedialUserAccess::find()->byUserId($userId)->withoutExpired()->exists();
    }
}

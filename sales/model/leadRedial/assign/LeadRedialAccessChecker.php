<?php

namespace sales\model\leadRedial\assign;

use sales\helpers\setting\SettingHelper;
use sales\model\leadRedial\entity\CallRedialUserAccess;

class LeadRedialAccessChecker
{
    public function exist(int $userId): bool
    {
        return CallRedialUserAccess::find()
            ->andWhere(['crua_user_id' => $userId])
            ->andWhere([
                '>',
                'crua_created_dt',
                (new \DateTimeImmutable())
                    ->modify('- ' . SettingHelper::getLeadRedialAccessExpiredSeconds() . ' seconds')
                    ->format('Y-m-d H:i:s')
            ])
            ->exists();
    }
}

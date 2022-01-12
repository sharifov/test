<?php

namespace src\model\leadRedial\entity;

use src\helpers\setting\SettingHelper;

/**
* @see CallRedialUserAccess
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function withExpired(): self
    {
        return $this->andWhere([
            '<=',
            'crua_created_dt',
            (new \DateTimeImmutable())
                ->modify('- ' . SettingHelper::getRedialUserAccessExpiredSecondsLimit() . ' seconds')
                ->format('Y-m-d H:i:s')
        ]);
    }

    public function withoutExpired(): self
    {
        return $this->andWhere([
            '>',
            'crua_created_dt',
            (new \DateTimeImmutable())
                ->modify('- ' . SettingHelper::getRedialUserAccessExpiredSecondsLimit() . ' seconds')
                ->format('Y-m-d H:i:s')
        ]);
    }

    public function byUserId(int $userId): self
    {
        return $this->andWhere(['crua_user_id' => $userId]);
    }

    public function byLeadId(int $leadId): self
    {
        return $this->andWhere(['crua_lead_id' => $leadId]);
    }

    /**
    * @return CallRedialUserAccess[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return CallRedialUserAccess|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

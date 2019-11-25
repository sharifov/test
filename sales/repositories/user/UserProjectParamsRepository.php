<?php

namespace sales\repositories\user;

use common\models\UserProjectParams;

class UserProjectParamsRepository
{

    /**
     * @param string $phone
     * @return UserProjectParams|null
     */
    public function getByPhone(string $phone): ?UserProjectParams
    {
        return UserProjectParams::find()
            ->where(['upp_tw_phone_number' => $phone])
            ->limit(1)
            ->one();
    }

    /**
     * @param int|null $projectId
     * @param string|null $from
     * @param string|null $to
     * @return UserProjectParams|null
     */
    public function find(?int $projectId, ?string $from, ?string $to): ?UserProjectParams
    {
        $upp = null;

        if ($projectId && $from) {
            $agentId = (int)str_replace('client:seller', '', $from);
            if ($agentId) {
                $upp = UserProjectParams::find()->where(['upp_user_id' => $agentId, 'upp_project_id' => $projectId])->one();
            }
        }

        if (!$upp) {
            $upp = $this->getByPhone($from);
        }

        if (!$upp) {
            $upp = $this->getByPhone($to);
        }

        return $upp;

    }

    /**
     * @param string|null $phone
     * @return array ex.[1,34,54]
     */
    public function findUsersIdByPhone(?string $phone): array
    {
        if (!$phone) {
            return [];
        }
        $users = [];
        $params = UserProjectParams::find()->where(['upp_tw_phone_number' => $phone])->all();
        foreach ($params as $param) {
            $users[] = $param->upp_user_id;
        }
        return $users;
    }

}
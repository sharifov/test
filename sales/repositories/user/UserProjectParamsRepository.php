<?php

namespace sales\repositories\user;

use common\models\UserProjectParams;

class UserProjectParamsRepository
{
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
//        $params = UserProjectParams::find()->where(['upp_tw_phone_number' => $phone])->all();
        $params = UserProjectParams::find()->select(['upp_user_id'])->byPhone($phone, false)->asArray()->all();
        foreach ($params as $param) {
//            $users[] = $param->upp_user_id;
            $users[] = (int)$param['upp_user_id'];
        }
        return $users;
    }
}

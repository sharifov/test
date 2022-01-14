<?php

namespace src\model\user\reports\stats;

use src\model\userModelSetting\entity\UserModelSetting;

class SessionFilterStorage
{
    public function find(int $userId, string $type): array
    {
        $filter = UserModelSetting::find()
            ->select(['ums_settings_json'])
            ->andWhere([
                'ums_user_id' => $userId,
                'ums_class' => $type
            ])
            ->one();

        if (!$filter) {
            return [];
        }

        return $filter->ums_settings_json;
    }

    public function add(int $userId, string $type, array $data): void
    {
        $filter = UserModelSetting::find()
            ->andWhere([
                'ums_user_id' => $userId,
                'ums_class' => $type
            ])
            ->one();

        if (!$filter) {
            $filter = new UserModelSetting([
                'ums_user_id' => $userId,
                'ums_class' => $type,
            ]);
        }

        $filter->ums_settings_json = $data;

        $filter->save(false);
    }

    public function remove(int $userId, string $type): void
    {
        $filter = UserModelSetting::find()
            ->andWhere([
                'ums_user_id' => $userId,
                'ums_class' => $type
            ])
            ->one();

        if (!$filter) {
            return;
        }

        $filter->delete();
    }
}

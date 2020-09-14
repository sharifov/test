<?php

namespace sales\model\userVoiceMail\entity;

class UserVoiceMailQuery
{
    public static function getListByUser(int $userId): array
    {
        return UserVoiceMail::find()
            ->select(['uvm_name', 'uvm_id'])
            ->andWhere(['uvm_user_id' => $userId])
            ->indexBy('uvm_id')
            ->column();
    }
}

<?php

namespace sales\model\call\helper;

use common\models\Call;
use common\models\Department;
use sales\helpers\setting\SettingHelper;

class CallHelper
{
    public static function getTypeDescription(Call $call): string
    {
        $description = '';
        if ($call->isIn()) {
            $description = 'Incoming';
        } elseif ($call->isOut()) {
            $description = 'Outgoing';
        } elseif ($call->isJoin()) {
            $description = 'Join';
            if ($call->c_source_type_id === Call::SOURCE_LISTEN) {
                $description .= ': Listen';
            } elseif ($call->c_source_type_id === Call::SOURCE_COACH) {
                $description .= ': Coach';
            } elseif ($call->c_source_type_id === Call::SOURCE_BARGE) {
                $description .= ': Barge';
            }
        } elseif ($call->isReturn()) {
            $description = 'Return';
        }
        return $description;
    }

    public static function warmTransferTimeout(?int $departmentId): int
    {
        if (!$departmentId) {
            return SettingHelper::warmTransferTimeout();
        }
        $department = Department::findOne($departmentId);
        if (!$department) {
            return SettingHelper::warmTransferTimeout();
        }
        $params = $department->getParams();
        if (!$params) {
            return SettingHelper::warmTransferTimeout();
        }
        $timeout = $params->warmTransferSettings->timeout;
        if ($timeout !== null) {
            return $timeout;
        }
        return SettingHelper::warmTransferTimeout();
    }

    public static function warmTransferAutoUnholdEnabled(?int $departmentId): bool
    {
        if (!$departmentId) {
            return SettingHelper::warmTransferAutoUnholdEnabled();
        }
        $department = Department::findOne($departmentId);
        if (!$department) {
            return SettingHelper::warmTransferAutoUnholdEnabled();
        }
        $params = $department->getParams();
        if (!$params) {
            return SettingHelper::warmTransferAutoUnholdEnabled();
        }
        $autoUnhold = $params->warmTransferSettings->autoUnholdEnabled;
        if ($autoUnhold !== null) {
            return $autoUnhold;
        }
        return SettingHelper::warmTransferAutoUnholdEnabled();
    }
}

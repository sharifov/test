<?php

namespace modules\shiftSchedule\src\services;

use modules\shiftSchedule\src\entities\userShiftAssign\repository\UserShiftAssignRepository;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use modules\shiftSchedule\src\forms\UserShiftMultipleAssignForm;

class UserShiftAssignService
{
    public function multipleAssign($shiftIds, $userIds, $formAction)
    {
        switch ($formAction) {
            case UserShiftMultipleAssignForm::ACTION_ADD:
                if ($shiftIds) {
                    $this->multipleAddAssign($shiftIds, $userIds);
                }
                break;

            case UserShiftMultipleAssignForm::ACTION_REPLACE:
                if ($shiftIds) {
                    $this->multipleReplaceAssign($shiftIds, $userIds);
                }
                break;

            case UserShiftMultipleAssignForm::ACTION_REMOVE:
                if ($shiftIds) {
                    UserShiftAssign::deleteAll(['usa_user_id' => $userIds, 'usa_sh_id' => $shiftIds]);
                } else {
                    UserShiftAssign::deleteAll(['usa_user_id' => $userIds]);
                }
                break;
        }
    }

    private function multipleAddAssign($shiftIds, $userIds)
    {
        foreach ($userIds as $userId) {
            foreach ($shiftIds as $shiftId) {
                if (!UserShiftAssign::find()->andWhere(['usa_user_id' => $userId, 'usa_sh_id' => (int)$shiftId])->exists()) {
                    $userShiftAssign = UserShiftAssign::create($userId, (int)$shiftId);
                    (new UserShiftAssignRepository($userShiftAssign))->save();
                }
            }
        }
    }

    private function multipleReplaceAssign($shiftIds, $userIds)
    {
        UserShiftAssign::deleteAll(['usa_user_id' => $userIds]);
        foreach ($userIds as $userId) {
            foreach ($shiftIds as $shiftId) {
                $userShiftAssign = UserShiftAssign::create($userId, (int)$shiftId);
                (new UserShiftAssignRepository($userShiftAssign))->save();
            }
        }
    }
}

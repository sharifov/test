<?php

namespace sales\services\client;

use common\models\DepartmentPhoneProject;
use common\models\UserProjectParams;

/**
 * Class InternalPhoneGuard
 *
 * @property $internalPhones
 */
class InternalPhoneGuard
{
    public $internalPhones;

    /**
     * @param string $phone
     */
    public function guard(string $phone): void
    {
        if (in_array($phone, $this->getInternalPhones(), true)) {
            throw new InternalPhoneException();
        }
    }

    private function getInternalPhones(): array
    {
        if ($this->internalPhones !== null) {
            return $this->internalPhones;
        }
        $this->internalPhones = array_merge([], $this->getDepartmentPhones(), $this->getUserProjectParams());
        return $this->internalPhones;
    }

    /**
     * @return array
     */
    private function getDepartmentPhones(): array
    {
//        $phones = [];
//        foreach (DepartmentPhoneProject::find()->select(['dpp_phone_number'])->asArray()->all() as $phone) {
//            if ($phone['dpp_phone_number']) {
//                $phones[] = $phone['dpp_phone_number'];
//            }
//        }
//        return $phones;
        return DepartmentPhoneProject::find()->select(['pl_phone_number'])->innerJoinWith('phoneList', false)->column();
    }

    /**
     * @return array
     */
    private function getUserProjectParams(): array
    {
//        $phones = [];
//        foreach (UserProjectParams::find()->select(['upp_tw_phone_number'])->asArray()->all() as $phone) {
//            if ($phone['upp_tw_phone_number']) {
//                $phones[] = $phone['upp_tw_phone_number'];
//            }
//        }
//        return $phones;
        return UserProjectParams::find()->select(['pl_phone_number'])->innerJoinWith('phoneList', false)->column();
    }
}

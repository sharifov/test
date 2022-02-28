<?php

namespace src\model\phoneNumberRedial;

use src\model\phoneNumberRedial\entity\PhoneNumberRedial;

class PhoneNumberRedialRepository
{
    public function save(PhoneNumberRedial $model): int
    {
        if (!$model->save()) {
            throw new \RuntimeException($model->getErrorSummary(true)[0]);
        }
        return $model->pnr_id;
    }
}

<?php

namespace src\model\clientUserReturn\entity;

class ClientUserReturnRepository
{
    public function save(ClientUserReturn $model): void
    {
        if (!$model->save()) {
            throw new \RuntimeException('ClientUserReturn saving failed: ' . $model->getErrorSummary(true)[0]);
        }
    }
}

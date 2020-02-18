<?php

namespace modules\qaTask\src\guard;

use sales\access\EmployeeProjectAccess;
use yii\web\ForbiddenHttpException;

class QaTaskGuard
{
    /**
     * @param int|null $projectId
     * @param int|null $userId
     * @throws ForbiddenHttpException
     */
    public static function guard(?int $projectId, ?int $userId): void
    {
        try {
            EmployeeProjectAccess::guard($projectId, $userId);
        } catch (\DomainException $e) {
            throw new ForbiddenHttpException($e->getMessage());
        }
    }
}

<?php

namespace modules\qaTask\src\entities\qaTaskStatusLog;

use yii\db\ActiveQuery;

/**
 * @see QaTaskStatusLog
 */
class Scopes extends ActiveQuery
{
    public function last(int $taskId): self
    {
        return $this
            ->andWhere(['tsl_task_id' => $taskId])
            ->orderBy(['tsl_id' => SORT_DESC])
            ->limit(1);
    }
}

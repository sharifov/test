<?php

namespace modules\qaTask\src\entities\qaTask;

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;

/**
 * @see QaTask
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function projects(array $projects): self
    {
        return $this->andWhere(['t_project_id' => $projects]);
    }

    public function queueProcessing(): self
    {
        return $this->statuses(array_keys(QaTaskStatus::getProcessingQueueList()));
    }

    public function statuses(array $statuses): self
    {
        $condition = ['OR'];
        foreach ($statuses as $status) {
            if (!QaTaskStatus::isExist($status)) {
                throw new \InvalidArgumentException('Undefined Qa Task status: ' . $status);
            }
            $condition[] = ['t_status_id' => $status];
        }
        return $this->andWhere($condition);
    }

    public function pending(): self
    {
        return $this->andWhere(['t_status_id' => QaTaskStatus::PENDING]);
    }

    public function processing(): self
    {
        return $this->andWhere(['t_status_id' => QaTaskStatus::PROCESSING]);
    }

    public function escalated(): self
    {
        return $this->andWhere(['t_status_id' => QaTaskStatus::ESCALATED]);
    }

    public function closed(): self
    {
        return $this->andWhere(['t_status_id' => QaTaskStatus::CLOSED]);
    }

    public function unAssigned(): self
    {
        return $this->andWhere(['t_assigned_user_id' => null]);
    }

    public function assigned(int $userId): self
    {
        return $this->andWhere(['t_assigned_user_id' => $userId]);
    }

    public function anyAssigned():self
    {
        return $this->andWhere(['IS NOT', 't_assigned_user_id',  null]);
    }
}

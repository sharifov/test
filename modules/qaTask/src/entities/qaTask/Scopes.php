<?php

namespace modules\qaTask\src\entities\qaTask;

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;

/**
 * @see QaTask
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function queueProcessing(): self
    {
        return $this->andWhere(['OR',
            ['t_status_id' => QaTaskStatus::PROCESSING],
            ['t_status_id' => QaTaskStatus::ESCALATED],
        ]);
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

    public function assigned():self
    {
        return $this->andWhere(['IS NOT', 't_assigned_user_id',  null]);
    }
}

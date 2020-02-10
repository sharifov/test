<?php

namespace modules\qaTask\src\entities\qaTask;

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;

/**
 * @see QaTask
 */
class Scopes extends \yii\db\ActiveQuery
{
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
}

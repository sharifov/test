<?php

namespace common\models\query;

use common\models\QcallConfig;

/**
 * This is the ActiveQuery class for [[QcallConfig]].
 *
 * @see QcallConfig
 */
class QcallConfigQuery extends \yii\db\ActiveQuery
{
    /**
     * @param int|null $status
     * @param int|null $callCount
     * @return QcallConfig|null
     */
    public function config(?int $status, ?int $callCount):? QcallConfig
    {
        return $this->andWhere(['qc_status_id' => $status])
            ->andWhere(['<=', 'qc_call_att', $callCount])
            ->orderBy(['qc_call_att' => SORT_DESC])
            ->limit(1)
            ->one();
    }
}

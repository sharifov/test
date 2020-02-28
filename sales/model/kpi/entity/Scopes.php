<?php

namespace sales\model\kpi\entity;

/**
 * This is the ActiveQuery class for [[KpiUserPerformance]].
 *
 * @see KpiUserPerformance
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return KpiUserPerformance[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return KpiUserPerformance|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

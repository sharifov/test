<?php

namespace sales\model\kpi\entity\kpiProductCommission;

/**
 * This is the ActiveQuery class for [[KpiProductCommission]].
 *
 * @see KpiProductCommission
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return KpiProductCommission[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return KpiProductCommission|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

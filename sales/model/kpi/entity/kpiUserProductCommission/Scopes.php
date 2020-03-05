<?php

namespace sales\model\kpi\entity\kpiUserProductCommission;

/**
 * This is the ActiveQuery class for [[KpiUserProductCommission]].
 *
 * @see KpiUserProductCommission
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return KpiUserProductCommission[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return KpiUserProductCommission|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

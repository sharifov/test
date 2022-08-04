<?php

namespace modules\smartLeadDistribution\src\entities;

/**
 * This is the ActiveQuery class for [[LeadRatingParameter]].
 *
 * @see LeadRatingParameter
 */
class LeadRatingParameterScopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return LeadRatingParameter[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return LeadRatingParameter|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

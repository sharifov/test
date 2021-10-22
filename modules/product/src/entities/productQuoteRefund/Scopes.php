<?php

namespace modules\product\src\entities\productQuoteRefund;

/**
 * This is the ActiveQuery class for [[ProductQuoteRefund]].
 *
 * @see ProductQuoteRefund
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProductQuoteRefund[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProductQuoteRefund|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byProductQuoteId(int $id): self
    {
        return $this->andWhere(['pqr_product_quote_id' => $id]);
    }

    public function byStatuses(array $statuses): self
    {
        return $this->andWhere(['pqr_status_id' => $statuses]);
    }

    public function excludeStatuses(array $statuses): self
    {
        return $this->andWhere(['NOT IN', 'pqr_status_id', $statuses]);
    }
}

<?php

namespace sales\model\emailReviewQueue\entity;

/**
 * This is the ActiveQuery class for [[EmailReviewQueue]].
 *
 * @see EmailReviewQueue
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return EmailReviewQueue[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EmailReviewQueue|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function filterByStatuses(array $statuses): self
    {
        return $this->andFilterWhere(['erq_status_id' => $statuses]);
    }
}

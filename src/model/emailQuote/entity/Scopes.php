<?php

namespace src\model\emailQuote\entity;

/**
 * This is the ActiveQuery class for [[EmailQuote]].
 *
 * @see EmailQuote
 */
class Scopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return EmailQuote[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return EmailQuote|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byEmailId(int $id): self
    {
        return $this->andWhere(['eq_email_id' => $id]);
    }
}

<?php

namespace src\model\emailQuote\entity;

/**
 * This is the ActiveQuery class for [[EmailQuote]].
 *
 * @see EmailQuote
 */
class EmailQuoteQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

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
}

<?php

namespace src\model\quoteLabel\entity;

/**
* @see QuoteLabel
*/
class QuoteLabelScopes extends \yii\db\ActiveQuery
{
    /**
    * @return QuoteLabel[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return QuoteLabel|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

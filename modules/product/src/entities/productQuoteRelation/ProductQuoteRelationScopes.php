<?php

namespace modules\product\src\entities\productQuoteRelation;

/**
* @see ProductQuoteRelation
*/
class ProductQuoteRelationScopes extends \yii\db\ActiveQuery
{
    /**
    * @return ProductQuoteRelation[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ProductQuoteRelation|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

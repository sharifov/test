<?php

namespace sales\model\visitorSubscription\entity;

/**
* @see VisitorSubscription
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return VisitorSubscription[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return VisitorSubscription|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

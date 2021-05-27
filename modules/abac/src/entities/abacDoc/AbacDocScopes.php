<?php

namespace modules\abac\src\entities\abacDoc;

/**
* @see AbacDoc
*/
class AbacDocScopes extends \yii\db\ActiveQuery
{
    /**
    * @return AbacDoc[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return AbacDoc|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

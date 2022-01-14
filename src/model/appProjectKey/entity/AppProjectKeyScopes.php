<?php

namespace src\model\appProjectKey\entity;

/**
* @see AppProjectKey
*/
class AppProjectKeyScopes extends \yii\db\ActiveQuery
{
    /**
    * @return AppProjectKey[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return AppProjectKey|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

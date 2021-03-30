<?php

namespace modules\attraction\models;

class Scopes extends \yii\db\ActiveQuery
{
    /**
     * @return Attraction[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @return Attraction|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

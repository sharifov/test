<?php

namespace modules\flight\models\query;

use modules\flight\models\FlightRequest;

/**
* @see \modules\flight\models\FlightRequest
*/
class FlightRequestQuery extends \yii\db\ActiveQuery
{
    public static function existRequestByHash(string $hash): bool
    {
        return FlightRequest::find()->where(['fr_hash' => $hash])->exists();
    }
}

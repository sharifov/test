<?php

namespace modules\flight\models\query;

use modules\flight\models\FlightRequest;

/**
* @see \modules\flight\models\FlightRequest
*/
class FlightRequestQuery extends \yii\db\ActiveQuery
{
    public static function existActiveRequestByHash(string $hash): bool
    {
        return FlightRequest::find()->where(['fr_hash' => $hash])->active()->exists();
    }

    public function active(): self
    {
        return $this->andWhere(['fr_status_id' => FlightRequest::getActiveStatusesList()]);
    }
}

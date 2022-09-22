<?php

namespace modules\quoteAward\src\forms;

use yii\base\Model;

class ImportGdsDumpForm extends Model
{
    public $tripId;
    public $gds;
    public $reservationDump;

    public function rules(): array
    {
        return [
            [['tripId', 'gds', 'reservationDump'], 'required'],
        ];
    }
}

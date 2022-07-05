<?php

namespace src\model\sale;

use common\components\validators\IsArrayValidator;
use yii\base\Model;

class SaleDetail extends Model
{
    public $processingTeamsStatus = [];
    public $notes = [];
    public $authList = [];

    public function rules(): array
    {
        return [
            [
                ['processingTeamsStatus', 'notes', 'authList'],
                IsArrayValidator::class,
            ]
        ];
    }
}

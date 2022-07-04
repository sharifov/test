<?php

namespace src\model\sale;

use yii\base\Model;

class SaleDetail extends Model
{
    public array $processingTeamsStatus = [];
    public array $notes = [];
    public array $authList = [];

    public function rules(): array
    {
        return [
            [
                ['processingTeamsStatus', 'notes', 'authList'],
                'safe',
            ]
        ];
    }
}

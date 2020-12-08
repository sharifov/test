<?php

namespace sales\forms\clientChat;

use sales\model\clientChat\entity\ClientChat;
use yii\base\Model;
use yii\helpers\Json;

class MultipleUpdateForm extends Model
{
    public $chatIds;

    public $statusId;


    public function rules()
    {
        return [
            ['statusId', 'integer'],
            ['statusId', 'filter', 'filter' => 'intval'],
            ['statusId', 'in', 'range' => array_keys(ClientChat::getStatusList())],

            ['chatIds', 'filter', 'filter' => static function ($value) {
                return Json::decode($value);
            }],
            ['chatIds', 'required'],
            ['chatIds', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['chatIds', 'each', 'rule' => ['exist', 'targetClass' => ClientChat::class, 'targetAttribute' => 'cch_id']],
        ];
    }
}

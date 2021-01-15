<?php

namespace sales\forms\clientChat;

use sales\model\clientChat\entity\ClientChat;
use yii\base\Model;
use yii\helpers\Json;

/**
 * Class MultipleCloseForm
 */
class MultipleCloseForm extends Model
{
    public $chatIds;
    public $toArchive;

    public function rules(): array
    {
        return [
            [['chatIds', 'toArchive'], 'required'],
            ['chatIds', 'filter', 'filter' => static function ($value) {
                return Json::decode($value);
            }],
            ['chatIds', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['chatIds', 'each', 'rule' => ['exist', 'targetClass' => ClientChat::class, 'targetAttribute' => 'cch_id']],

            ['toArchive', 'boolean'],
            ['toArchive', 'default', 'value' => false],
            ['toArchive', 'filter', 'filter' => 'boolval'],
            ['toArchive', 'compare', 'compareValue' => true, 'message' => 'Please fix the following errors: Check to confirm'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'toArchive' => '',
        ];
    }
}

<?php

namespace sales\forms\clientChat;

use common\components\validators\IsArrayValidator;
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
            ['chatIds', 'filter', 'filter' => static function ($value) {
                return Json::decode($value);
            }],
            ['chatIds', IsArrayValidator::class, 'skipOnEmpty' => false],
            ['chatIds', 'required', 'isEmpty' => function ($value) {
                return !count($value);
            }, 'message' => 'Please select chats'],
            ['chatIds', 'each', 'rule' => ['filter', 'filter' => 'intval']],
            ['chatIds', 'each', 'rule' => ['exist', 'targetClass' => ClientChat::class, 'targetAttribute' => 'cch_id']],

            ['toArchive', 'required'],
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

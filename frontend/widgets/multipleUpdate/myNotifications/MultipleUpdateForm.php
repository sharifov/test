<?php

namespace frontend\widgets\multipleUpdate\myNotifications;

use common\components\validators\IsArrayValidator;
use yii\base\Model;

/**
 * Class MultipleUpdateForm
 *
 * @property int[] $ids
 */
class MultipleUpdateForm extends Model
{
    public $ids;

    public function rules(): array
    {
        return [
            ['ids', 'required', 'message' => 'Not selected rows'],
            ['ids', IsArrayValidator::class, 'skipOnEmpty' => false],
            ['ids', 'each', 'rule' => ['integer']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ids' => 'Ids',
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

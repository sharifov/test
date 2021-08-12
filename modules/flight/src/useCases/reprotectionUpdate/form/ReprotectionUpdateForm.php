<?php

namespace modules\flight\src\useCases\reprotectionUpdate\form;

use yii\base\Model;

/**
 * Class ReprotectionUpdateForm
 *
 * @property string $type
 * @property array $data
 */

class ReprotectionUpdateForm extends Model
{
    public $type;
    public $data;
    public $reprotection_quote_gid;

    public function rules(): array
    {
        return [
            [['type'], 'required'],
            [['type'], 'string', 'max' => 30],
            [['data'], 'validateData'],
        ];
    }

    public function validateData()
    {
        foreach ($this->data as $key => $element) {
            if (empty($element)) {
                $this->addError('data', "Invalid " . $key);
            }
        }
    }

    public function formName(): string
    {
        return '';
    }
}

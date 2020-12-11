<?php

namespace sales\model\client\useCase\excludeInfo;

use yii\base\Model;

/**
 * Class Result
 *
 * @property $exclude_type
 * @property $excluded
 * @property $ppn
 */
class Result extends Model
{
    public $exclude_type;
    public $excluded;
    public $ppn;

    public function rules(): array
    {
        return [
            ['exclude_type', 'in', 'range' => ['A', 'I']],

            ['excluded', 'required'],
            ['excluded', 'boolean'],

            ['ppn', 'string', 'max' => 10],
        ];
    }

    public function isExcluded(): bool
    {
        return $this->excluded ? true : false;
    }

    public function formName(): string
    {
        return '';
    }
}

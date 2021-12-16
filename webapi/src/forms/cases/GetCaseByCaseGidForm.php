<?php

namespace webapi\src\forms\cases;

use yii\base\Model;

/**
 * Class GetCaseByCaseGidForm
 * @package webapi\src\forms\cases
 *
 * @property int $gid
 */
class GetCaseByCaseGidForm extends Model
{
    public $gid;

    public function rules(): array
    {
        return [
            ['gid', 'required'],
            ['gid', 'string', 'max' => 50],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

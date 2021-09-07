<?php

namespace webapi\src\forms\cases;

use yii\base\Model;

/**
 * Class GetCaseByCaseGidForm
 * @package webapi\src\forms\cases
 *
 * @property int $case_gid
 */
class GetCaseByCaseGidForm extends Model
{
    public $case_gid;

    public function rules(): array
    {
        return [
            ['case_gid', 'required'],
            ['case_gid', 'string', 'max' => 50],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

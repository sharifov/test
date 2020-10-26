<?php

namespace sales\model\department\departmentPhoneProject\useCases\loadCsv;

use yii\base\Model;

class DepartmentPhonesForm extends Model
{
    public $file;

    public function rules(): array
    {
        return [
            ['file', 'required'],
            ['file', 'file'],
        ];
    }
}

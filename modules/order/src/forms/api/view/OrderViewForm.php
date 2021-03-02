<?php

namespace modules\order\src\forms\api\view;

use yii\base\Model;

/**
 * Class OrderViewForm
 * @package modules\order\src\forms\api\view
 *
 * @property string $gid
 */
class OrderViewForm extends Model
{
    public string $gid = '';

    public function rules()
    {
        return [
            ['gid', 'required'],
            ['gid', 'string']
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

<?php

namespace modules\order\src\forms\api\cancel;

use modules\order\src\entities\order\Order;
use yii\base\Model;

/**
 * Class CancelForm
 *
 * @property $gid
 */
class CancelForm extends Model
{
    public $gid;

    public function rules(): array
    {
        return [
            ['gid', 'required'],
            ['gid', 'string'],
            ['gid', 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['gid' => 'or_gid']],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

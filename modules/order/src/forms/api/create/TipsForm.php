<?php

namespace modules\order\src\forms\api\create;

/**
 * Class TipsForm
 * @package modules\order\src\forms\api\create
 *
 * @property float|null $total_amount
 */
class TipsForm extends \yii\base\Model
{
    public $total_amount;

    public function rules(): array
    {
        return [
            ['total_amount', 'number'],
            ['total_amount', 'filter', 'filter' => 'floatval', 'skipOnEmpty' => true],
        ];
    }

    public function formName(): string
    {
        return 'tips';
    }
}

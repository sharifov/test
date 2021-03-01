<?php

namespace modules\order\src\useCase\orderComplete;

use yii\base\Model;

class CompleteForm extends Model
{
    public $orderId;

    public $description;

    public function __construct(int $orderId, $config = [])
    {
        parent::__construct($config);
        $this->orderId = $orderId;
    }

    public function rules(): array
    {
        return [
            ['description', 'string', 'max' => 255],
        ];
    }
}

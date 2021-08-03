<?php

namespace modules\flight\src\useCases\reprotectionDecision;

use yii\base\Model;

/**
 * Class DecisionForm
 *
 * @property int $reprotection_id
 * @property int $type_id
 * @property $flight_product_quote
 */
class DecisionForm extends Model
{
    public const TYPE_CONFIRM = 1;
    public const TYPE_MODIFY = 2;
    public const TYPE_REFUND = 3;

    public const TYPES = [
        self::TYPE_CONFIRM => 'confirm',
        self::TYPE_MODIFY => 'modify',
        self::TYPE_REFUND => 'refund',
    ];

    public $reprotection_id;
    public $type_id;
    public $flight_product_quote;

    public function rules(): array
    {
        return [
            ['reprotection_id', 'required'],
            ['reprotection_id', 'integer'],

            ['type_id', 'required'],
            ['type_id', 'filter', 'filter' => 'intval', 'skipOnError' => true, 'skipOnEmpty' => true],
            ['type_id', 'in', 'range' => array_keys(self::TYPES)],

            ['flight_product_quote', 'required', 'when' => function () {
                return $this->isModify();
            }],
            ['flight_product_quote', 'safe'], // todo
        ];
    }

    public function isConfirm(): bool
    {
        return $this->type_id === self::TYPE_CONFIRM;
    }

    public function isModify(): bool
    {
        return $this->type_id === self::TYPE_MODIFY;
    }

    public function isRefund(): bool
    {
        return $this->type_id === self::TYPE_REFUND;
    }

    public function formName(): string
    {
        return '';
    }
}

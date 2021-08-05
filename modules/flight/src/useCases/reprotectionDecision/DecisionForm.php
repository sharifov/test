<?php

namespace modules\flight\src\useCases\reprotectionDecision;

use yii\base\Model;

/**
 * Class DecisionForm
 *
 * @property string $origin_quote_gid
 * @property string $reprotection_quote_gid
 * @property string $type
 * @property string $flight_product_quote
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

    public $origin_quote_gid;
    public $reprotection_quote_gid;
    public $type;
    public $flight_product_quote;

    public function rules(): array
    {
        return [

            ['origin_quote_gid', 'required'],
            [['origin_quote_gid', 'reprotection_quote_gid'], 'string', 'max' => 32],

            ['type', 'required'],
            ['type', 'in', 'range' => self::TYPES],

            ['reprotection_quote_gid', 'required', 'when' => function () {
                return $this->isModify() || $this->isConfirm();
            }],

            ['flight_product_quote', 'required', 'when' => function () {
                return $this->isModify();
            }],
            ['flight_product_quote', 'safe'], // todo
        ];
    }

    public function isConfirm(): bool
    {
        return $this->type === self::TYPES[self::TYPE_CONFIRM];
    }

    public function isModify(): bool
    {
        return $this->type === self::TYPES[self::TYPE_MODIFY];
    }

    public function isRefund(): bool
    {
        return $this->type === self::TYPES[self::TYPE_REFUND];
    }

    public function formName(): string
    {
        return '';
    }
}

<?php

namespace modules\flight\src\useCases\reprotectionDecision;

use yii\base\Model;

/**
 * Class DecisionForm
 *
 * @property int $reprotectionId
 * @property string $type
 * @property $quote
 */
class DecisionForm extends Model
{
    public const TYPE_CONFIRM = 'confirm';
    public const TYPE_MODIFY = 'modify';
    public const TYPE_REFUND = 'refund';

    public const TYPES = [
        self::TYPE_CONFIRM => self::TYPE_CONFIRM,
        self::TYPE_MODIFY => self::TYPE_MODIFY,
        self::TYPE_REFUND => self::TYPE_REFUND,
    ];

    public $reprotectionId;
    public $type;
    public $quote;

    public function rules(): array
    {
        return [
            ['reprotectionId', 'required'],
            ['reprotectionId', 'integer'],

            ['type', 'required'],
            ['type', 'in', 'range' => array_keys(self::TYPES)],

            ['quote', 'required', 'when' => function () {
                return $this->isModify();
            }],
            ['quote', 'safe'], // todo
        ];
    }

    public function isConfirm(): bool
    {
        return $this->type === self::TYPE_CONFIRM;
    }

    public function isModify(): bool
    {
        return $this->type === self::TYPE_MODIFY;
    }

    public function isRefund(): bool
    {
        return $this->type === self::TYPE_REFUND;
    }

    public function formName(): string
    {
        return '';
    }
}

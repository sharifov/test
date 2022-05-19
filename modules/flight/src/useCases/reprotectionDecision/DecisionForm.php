<?php

namespace modules\flight\src\useCases\reprotectionDecision;

use common\components\validators\CheckJsonValidator;
use common\components\validators\IsArrayValidator;
use yii\base\Model;

/**
 * Class DecisionForm
 *
 * @property string $booking_id
 * @property string $type
 * @property string $reprotection_quote_gid
 * @property array $flight_product_quote
 */
class DecisionForm extends Model
{
    public const TYPE_CONFIRM = 'confirm';
    public const TYPE_MODIFY = 'modify';
    public const TYPE_REFUND = 'refund';

    public const TYPES = [
        self::TYPE_CONFIRM,
        self::TYPE_MODIFY,
        self::TYPE_REFUND,
    ];

    public $booking_id;
    public $type;
    public $reprotection_quote_gid;
    public $flight_product_quote;

    public function rules(): array
    {
        return [
            [['booking_id', 'reprotection_quote_gid'], 'required'],
            ['booking_id', 'string', 'min' => 7, 'max' => 10],

            ['type', 'required'],
            ['type', 'in', 'range' => self::TYPES],

            ['reprotection_quote_gid', 'string', 'max' => 32],

            ['flight_product_quote', 'required', 'when' => function () {
                return $this->isModify();
            }],
            ['flight_product_quote', CheckJsonValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
            ['flight_product_quote', IsArrayValidator::class, 'skipOnEmpty' => true, 'skipOnError' => true],
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

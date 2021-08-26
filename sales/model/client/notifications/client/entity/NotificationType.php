<?php

namespace sales\model\client\notifications\client\entity;

use yii\bootstrap4\Html;

/**
 * Class NotificationType
 *
 * @property int $value
 * @property string $type
 */
class NotificationType
{
    public const PRODUCT_QUOTE_CHANGE = 1;

    private const LIST = [
        self::PRODUCT_QUOTE_CHANGE => 'productQuoteChange',
    ];

    private const CSS_CLASS_LIST = [
        self::PRODUCT_QUOTE_CHANGE => 'info',
    ];

    private int $value;
    private string $type;

    private function __construct(int $value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    public static function productQuoteChange(): self
    {
        return new self(self::PRODUCT_QUOTE_CHANGE, self::LIST[self::PRODUCT_QUOTE_CHANGE]);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function getName(?int $value): string
    {
        return self::LIST[$value] ?? 'Undefined';
    }

    private static function getCssClass(?int $value): string
    {
        return self::CSS_CLASS_LIST[$value] ?? 'secondary';
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getCssClass($value)]
        );
    }
}

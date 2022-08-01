<?php

namespace src\model\client\notifications\client\entity;

use yii\bootstrap4\Html;

/**
 * Class NotificationType
 *
 * @property int $value
 * @property string $type
 */
class NotificationType
{
    public const PRODUCT_QUOTE_CHANGE_AUTO_DECISION_PENDING_EVENT = 1;
    public const PRODUCT_QUOTE_CHANGE_CLIENT_REMAINDER_NOTIFICATION_EVENT = 2;

    /**
     * @see \src\model\project\entity\params\ClientNotification property name must equal of this list
     */
    private const LIST = [
        self::PRODUCT_QUOTE_CHANGE_AUTO_DECISION_PENDING_EVENT => 'productQuoteChangeAutoDecisionPendingEvent',
        self::PRODUCT_QUOTE_CHANGE_CLIENT_REMAINDER_NOTIFICATION_EVENT => 'productQuoteChangeClientRemainderNotificationEvent',
    ];

    private const CSS_CLASS_LIST = [
        self::PRODUCT_QUOTE_CHANGE_AUTO_DECISION_PENDING_EVENT => 'info',
        self::PRODUCT_QUOTE_CHANGE_CLIENT_REMAINDER_NOTIFICATION_EVENT => 'info',
    ];

    private int $value;
    private string $type;

    private function __construct(int $value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    public static function fromEvent(object $event): self
    {
        $class = get_class($event);
        $className = lcfirst(self::getClassName($class));
        $list = array_flip(self::LIST);
        if (isset($list[$className])) {
            return new self($list[$className], $className);
        }
        throw new \InvalidArgumentException('Invalid notification type.');
    }

    private static function getClassName($classname): string
    {
        if ($pos = strrpos($classname, '\\')) {
            return substr($classname, $pos + 1);
        }
        return $pos;
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

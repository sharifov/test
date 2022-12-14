<?php

namespace modules\order\src\entities\order;

use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\bootstrap4\Html;

class OrderStatus
{
    public const NEW = 1;
    public const PENDING = 2;
    public const PROCESSING = 3;
    public const PREPARED = 4;
    public const COMPLETE = 5;
    public const CANCEL_PROCESSING = 6;
    public const ERROR = 7;
    public const DECLINED = 8;
    public const CANCELED = 9;
    public const CANCEL_FAILED = 10;

    private const LIST = [
        self::NEW => 'New',
        self::PENDING => 'Pending',
        self::PROCESSING => 'Processing',
        self::PREPARED => 'Prepared',
        self::COMPLETE => 'Complete',
        self::CANCEL_PROCESSING => 'Cancel processing',
        self::ERROR => 'Error',
        self::DECLINED => 'Declined',
        self::CANCELED => 'Canceled',
        self::CANCEL_FAILED => 'Cancel failed',
    ];

    private const CLASS_LIST = [
        self::NEW => 'info',
        self::PENDING => 'warning',
        self::PROCESSING => 'success',
        self::PREPARED => 'success',
        self::COMPLETE => 'success',
        self::CANCEL_PROCESSING => 'warning',
        self::ERROR => 'danger',
        self::DECLINED => 'danger',
        self::CANCELED => 'danger',
        self::CANCEL_FAILED => 'danger',
    ];

    public const ROUTE_RULES = [
        null => [
            self::NEW,
            self::PENDING,
            self::PROCESSING,
            self::CANCEL_FAILED,
        ],
        self::NEW => [
            self::PENDING,
            self::PROCESSING,
            self::DECLINED,
            self::CANCELED,
            self::CANCEL_FAILED,
        ],
        self::PENDING => [
            self::PREPARED,
            self::PROCESSING,
            self::CANCEL_PROCESSING,
            self::ERROR,
            self::CANCEL_FAILED,
        ],
        self::PROCESSING => [
            self::PREPARED,
            self::COMPLETE,
            self::CANCEL_PROCESSING,
            self::ERROR,
            self::DECLINED,
            self::CANCELED,
            self::CANCEL_FAILED,
        ],
        self::PREPARED => [
            self::COMPLETE,
            self::CANCEL_PROCESSING,
            self::ERROR,
            self::CANCELED,
            self::CANCEL_FAILED,
        ],
        self::COMPLETE => [
            self::CANCEL_PROCESSING,
            self::CANCELED,
            self::CANCEL_FAILED,
        ],
        self::CANCEL_PROCESSING  => [
            self::CANCELED
        ],
        self::ERROR  => [
            self::CANCEL_FAILED
        ],
        self::DECLINED  => [
        ],
        self::CANCELED  => [
        ],
        self::CANCEL_FAILED  => [
        ],
    ];

    public const ROUTE_ORDER_RULES = [
        self::PROCESSING => [
            ProductQuoteStatus::IN_PROGRESS,
            ProductQuoteStatus::BOOKED,
            ProductQuoteStatus::SOLD,
            ProductQuoteStatus::DELIVERED
        ]
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getClassName($value), 'data-toggle' => 'tooltip',  'data-original-title' => 'Order status']
        );
    }

    public static function getName(?int $value)
    {
        return self::LIST[$value] ?? 'Undefined';
    }

    private static function getClassName(?int $value): string
    {
        return self::CLASS_LIST[$value] ?? 'secondary';
    }

    public static function guard(?int $fromStatus, int $toStatus): void
    {
        if (!isset(self::ROUTE_RULES[$fromStatus])) {
            throw new \DomainException('Disallow transfer from ' . self::getName($fromStatus));
        }
        if (!in_array($toStatus, self::ROUTE_RULES[$fromStatus], true)) {
            throw new \DomainException('Disallow transfer from ' . self::getName($fromStatus) . ' to ' . self::getName($toStatus));
        }
    }

    public static function guardOrder(int $orderStatus, $productQuoteStatus): bool
    {
        if (!isset(self::ROUTE_ORDER_RULES[$orderStatus])) {
//          throw new \DomainException('Disallow transfer order status to ' . self::getName($orderStatus));
            return false;
        }
        if (!in_array($productQuoteStatus, self::ROUTE_ORDER_RULES[$orderStatus], true)) {
//          throw new \DomainException('Disallow transfer order status to ' . self::getName($orderStatus));
            return false;
        }
        return true;
    }
}

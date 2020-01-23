<?php

namespace sales\entities\cases;

use yii\helpers\Html;

class CasesStatus
{
    public const STATUS_PENDING     = 1;
    public const STATUS_PROCESSING  = 2;
    public const STATUS_FOLLOW_UP   = 5;
    public const STATUS_SOLVED      = 10;
    public const STATUS_TRASH       = 11;

    public const STATUS_LIST = [
        self::STATUS_PENDING        => 'Pending',
        self::STATUS_PROCESSING     => 'Processing',
        self::STATUS_FOLLOW_UP      => 'Follow Up',
        self::STATUS_SOLVED         => 'Solved',
        self::STATUS_TRASH          => 'Trash',
    ];

    public const STATUS_LIST_CLASS = [
        self::STATUS_PENDING        => 'll-pending',
        self::STATUS_PROCESSING     => 'll-processing',
        self::STATUS_FOLLOW_UP      => 'll-follow_up',
        self::STATUS_SOLVED         => 'll-sold',
        self::STATUS_TRASH          => 'll-trash',
    ];

    public const STATUS_ROUTE_RULES = [
        null => [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_FOLLOW_UP,
            self::STATUS_TRASH,
            self::STATUS_SOLVED,
        ],
        self::STATUS_PENDING => [
            self::STATUS_PROCESSING,
            self::STATUS_TRASH,
        ],
        self::STATUS_PROCESSING => [
            self::STATUS_PROCESSING,
            self::STATUS_FOLLOW_UP,
            self::STATUS_TRASH,
            self::STATUS_SOLVED,
        ],
        self::STATUS_FOLLOW_UP => [
            self::STATUS_PROCESSING,
            self::STATUS_TRASH,
        ],
        self::STATUS_TRASH => [
            self::STATUS_PROCESSING,
            self::STATUS_FOLLOW_UP,
            self::STATUS_SOLVED,
        ],
        self::STATUS_SOLVED => [
            self::STATUS_PROCESSING,
            self::STATUS_FOLLOW_UP,
            self::STATUS_TRASH,
        ],
    ];

    public const STATUS_REASON_LIST = [
        self::STATUS_FOLLOW_UP => [
            'No Answer' => 'No Answer',
            'Customer Reply Required' => 'Customer Reply Required',
            'TS Reply Required' => 'TS Reply Required',
            'Investigation Needed' => 'Investigation Needed',
            'Other' => 'Other'
        ],
        self::STATUS_TRASH => [
            'Wrong Number' => 'Wrong Number',
            'No assistance needed' => 'No assistance needed',
            'Other' => 'Other'
        ]
    ];

    /**
     * @param int|null $status
     * @return array
     */
    public static function getAllowTransferList(?int $status): array
    {
        $list = [];
        if (!isset(self::STATUS_ROUTE_RULES[$status])) {
            return $list;
        }
        foreach (self::STATUS_ROUTE_RULES[$status] as $item) {
            $list[$item] = self::getName($item);
        }
        return $list;
    }

    /**
     * @param int|null $fromStatus
     * @param int $toStatus
     */
    public static function guard(?int $fromStatus, int $toStatus): void
    {
        if (!isset(self::STATUS_ROUTE_RULES[$fromStatus])) {
            throw new \DomainException('Disallow transfer from ' . self::getName($fromStatus));
        }
        if (!in_array($toStatus, self::STATUS_ROUTE_RULES[$fromStatus], true)) {
            throw new \DomainException('Disallow transfer from ' . self::getName($fromStatus) . ' to ' . self::getName($toStatus));
        }
    }

    /**
     * @param int|null $status
     * @return string
     */
    public static function getName(?int $status): string
    {
        return self::STATUS_LIST[$status] ?? ($status ? 'Undefined' : '');
    }

    /**
     * @param int|null $status
     * @return string
     */
    public static function getClass(?int $status): string
    {
        return self::STATUS_LIST_CLASS[$status] ?? 'll-trash';
    }

    /**
     * @param int|null $status
     * @return string
     */
    public static function getLabel(?int $status): string
    {
        return Html::tag('span', self::getName($status), ['class' => 'label ' . self::getClass($status), 'style' => 'font-size: 13px']);
    }

    /**
     * @param int|null $status
     * @return array
     */
    public static function getReasonListByStatus(?int $status = null): array
    {
        return !empty(self::STATUS_REASON_LIST[$status]) ? self::STATUS_REASON_LIST[$status] : [];
    }
}
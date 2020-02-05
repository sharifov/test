<?php

namespace modules\product\src\entities\productQuote;

use yii\bootstrap4\Html;

class ProductQuoteStatus
{
    public const NEW			 = 1;
    public const PENDING         = 2;
    public const APPLIED		 = 3;
    public const IN_PROGRESS	 = 4;
    public const BOOKED			 = 5;
    public const SOLD			 = 6;
    public const DELIVERED		 = 7;
    public const ERROR		     = 8;
    public const EXPIRED		 = 9;
    public const DECLINED		 = 10;
    public const CANCELED		 = 11;

    public const LIST        = [
    	self::NEW			 => 'New',
        self::PENDING        => 'Pending',
		self::APPLIED		 => 'Applied',
        self::IN_PROGRESS    => 'In progress',
        self::BOOKED         => 'Booked',
        self::SOLD       	 => 'Sold',
		self::DELIVERED		 => 'Delivered',
		self::ERROR			 => 'Error',
		self::EXPIRED		 => 'Expired',
        self::DECLINED       => 'Declined',
        self::CANCELED       => 'Canceled',
    ];

    private const CLASS_LIST        = [
    	self::NEW			 => 'default',
        self::PENDING        => 'warning',
        self::APPLIED        => 'warning',
        self::IN_PROGRESS    => 'warning',
        self::BOOKED    	 => 'info',
        self::SOLD           => 'success',
        self::DELIVERED      => 'success',
        self::ERROR          => 'danger',
        self::EXPIRED        => 'danger',
        self::DECLINED       => 'danger',
        self::CANCELED       => 'danger',
    ];

	public CONST STATUS_CLASS_SPAN = [
		self::NEW => 'status-new',
		self::APPLIED => 'status-applied',
		self::DECLINED => 'status-declined',
		self::PENDING => 'status-send',
		self::IN_PROGRESS => 'status-opened'
	];

	public const ROUTE_RULES = [
        null => [
            self::NEW,
            self::PENDING,
            self::APPLIED,
            self::IN_PROGRESS,
        ],
        self::NEW => [
            self::PENDING,
            self::APPLIED,
            self::CANCELED,
            self::EXPIRED,
            self::DECLINED,
        ],
        self::PENDING => [
            self::IN_PROGRESS,
            self::APPLIED,
            self::CANCELED,
            self::EXPIRED,
            self::DECLINED,
            self::ERROR,
        ],
        self::APPLIED => [
            self::IN_PROGRESS,
            self::CANCELED,
            self::EXPIRED,
            self::DECLINED,
        ],
        self::IN_PROGRESS => [
            self::BOOKED,
            self::ERROR,
            self::CANCELED,
        ],
        self::BOOKED => [
            self::SOLD,
            self::ERROR,
            self::CANCELED,
        ],
        self::SOLD => [
            self::DELIVERED,
            self::ERROR,
            self::CANCELED,
        ],
        self::DELIVERED => [
            self::ERROR,
            self::CANCELED,
        ],
        self::ERROR => [
            self::IN_PROGRESS,
            self::BOOKED,
            self::SOLD,
            self::DELIVERED,
        ],
        self::CANCELED => [
            self::IN_PROGRESS,
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
            ['class' => 'badge badge-' . self::getClassName($value)]
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

	/**
	 * @param ProductQuote $productQuote
	 * @return string
	 * @throws \yii\base\InvalidConfigException
	 */
	public static function getStatusSpan(ProductQuote $productQuote): string
	{
		$class = self::STATUS_CLASS_SPAN[$productQuote->pq_status_id] ??  '';
		$label = self::LIST[$productQuote->pq_status_id] ?? '-';

		return '<span id="q-status-' . $productQuote->pq_id . '" class="quote__status '.$class.'" title="' . \Yii::$app->formatter->asDatetime($productQuote->pq_updated_dt) . '" data-toggle="tooltip"><i class="fa fa-circle"></i> <span>'.$label.'</span></span>';
	}

	/**
	 * @param int $status
	 * @return bool
	 */
	public static function isNews(int $status): bool
	{
		return $status === self::NEW;
	}

    /**
     * @param int $status
     * @return bool
     */
    public static function isBookable(int $status): bool
    {
        return in_array($status, [
            self::NEW,
            self::PENDING,
        ]);
    }

    /**
     * @param int|null $fromStatus
     * @param int $toStatus
     */
    public static function guard(?int $fromStatus, int $toStatus): void
    {
        if (!isset(self::ROUTE_RULES[$fromStatus])) {
            throw new \DomainException('Disallow transfer from ' . self::getName($fromStatus));
        }
        if (!in_array($toStatus, self::ROUTE_RULES[$fromStatus], true)) {
            throw new \DomainException('Disallow transfer from ' . self::getName($fromStatus) . ' to ' . self::getName($toStatus));
        }
    }
}

<?php

namespace sales\entities\cases;

use yii\helpers\Html;

class CasesStatusHelper
{

    public const STATUS_LIST = [
        Cases::STATUS_PENDING        => 'Pending',
        Cases::STATUS_PROCESSING     => 'Processing',
        Cases::STATUS_FOLLOW_UP      => 'Follow Up',
        Cases::STATUS_SOLVED         => 'Solved',
        Cases::STATUS_TRASH          => 'Trash',
    ];

    public const STATUS_LIST_CLASS = [
        Cases::STATUS_PENDING        => 'll-pending',
        Cases::STATUS_PROCESSING     => 'll-processing',
        Cases::STATUS_FOLLOW_UP      => 'll-follow_up',
        Cases::STATUS_SOLVED         => 'll-sold',
        Cases::STATUS_TRASH          => 'll-trash',
    ];

    /**
     * @param int|null $status
     * @return string
     */
    public static function getName(?int $status): string
    {
        return self::STATUS_LIST[$status] ?? '';
    }

    /**
     * @param int|null $status
     * @return string
     */
    public static function getClass(?int $status): string
    {
        return self::STATUS_LIST_CLASS[$status] ?? 'll-default';
    }

    /**
     * @param int|null $status
     * @return string
     */
    public static function getLabel(?int $status): string
    {
        return Html::tag('span', self::getName($status), ['class' => 'label ' . self::getClass($status), 'style' => 'font-size: 13px']);
    }

}
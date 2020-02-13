<?php

namespace modules\invoice\src\entities\invoice;

use yii\bootstrap4\Html;

class InvoiceStatusAction
{
    public const TEST = 1;

    private const LIST = [
        self::TEST => 'api/test/action',
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value)
        );
    }

    public static function getName(?int $value)
    {
        return self::LIST[$value] ?? 'Undefined';
    }
}

<?php

namespace  modules\fileStorage\src\grid\columns;

use modules\fileStorage\src\entity\fileLog\FileLogType;
use yii\grid\DataColumn;

/**
 * Class FileLogTypeColumn
 *
 * Ex.
    [
        'class' => \modules\fileStorage\src\grid\columns\FileLogTypeColumn::class,
    ],
 */
class FileLogTypeColumn extends DataColumn
{
    public $attribute = 'fl_type_id';

    public $format = 'fileLogType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = FileLogType::getList();
        }
    }
}

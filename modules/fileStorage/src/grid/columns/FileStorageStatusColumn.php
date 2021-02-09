<?php

namespace  modules\fileStorage\src\grid\columns;

use modules\fileStorage\src\entity\fileStorage\FileStorageStatus;
use yii\grid\DataColumn;

/**
 * Class FileStorageStatusColumn
 *
 * Ex.
    [
        'class' => \modules\fileStorage\src\grid\columns\FileStorageStatusColumn::class,
    ],
 */
class FileStorageStatusColumn extends DataColumn
{
    public $attribute = 'fs_status';

    public $format = 'fileStorageStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = FileStorageStatus::getList();
        }
    }
}

<?php

namespace  modules\fileStorage\src\grid\columns;

use modules\fileStorage\FileStorageSettings;
use yii\grid\DataColumn;

/**
 * Class FileStorageMimeTypeColumn
 *
 * Ex.
    [
        'class' => \modules\fileStorage\src\grid\columns\FileStorageMimeTypeColumn::class,
    ],
 */
class FileStorageMimeTypeColumn extends DataColumn
{
    public $attribute = 'fs_mime_type';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = FileStorageSettings::getMimeTypes();
        }
    }
}

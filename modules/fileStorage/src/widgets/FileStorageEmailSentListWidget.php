<?php

namespace modules\fileStorage\src\widgets;

use yii\base\Widget;

/**
 * Class FileStorageEmailSentListWidget
 */
class FileStorageEmailSentListWidget extends Widget
{
    public array $files = [];

    public function run(): string
    {
        return $this->render('email_sent_list', [
            'files' => $this->files,
        ]);
    }

    public static function create(array $files): string
    {
        return self::widget([
            'files' => $files,
        ]);
    }
}

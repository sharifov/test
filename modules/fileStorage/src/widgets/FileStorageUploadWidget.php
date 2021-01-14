<?php

namespace modules\fileStorage\src\widgets;

use modules\fileStorage\src\useCase\uploadFile\UploadForm;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * Class FileStorageUploadWidget
 *
 * @property string $url
 */
class FileStorageUploadWidget extends Widget
{
    public string $url;

    public function init()
    {
        parent::init();
        if (!$this->url) {
            throw new \InvalidArgumentException('url must be set.');
        }
    }

    public function run(): string
    {
        $form = new UploadForm();
        return $this->render('upload', ['form' => $form, 'url' => $this->url]);
    }

    public static function byLead(int $id): string
    {
        return self::widget([
            'url' => Url::to(['/file-storage/file-storage-upload/upload-by-lead', 'id' => $id])
        ]);
    }

    public static function byCase(int $id): string
    {
        return self::widget([
            'url' => Url::to(['/file-storage/file-storage-upload/upload-by-case', 'id' => $id])
        ]);
    }
}

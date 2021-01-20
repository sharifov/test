<?php

namespace modules\fileStorage\src\widgets;

use modules\fileStorage\src\entity\fileCase\FileCaseQuery;
use modules\fileStorage\src\entity\fileLead\FileLeadQuery;
use modules\fileStorage\src\services\url\UrlGenerator;
use yii\base\Widget;

/**
 * Class FileStorageListWidget
 *
 * @property array $files
 * @property string $uploadWidget
 * @property UrlGenerator|null $urlGenerator
 */
class FileStorageListWidget extends Widget
{
    public array $files = [];
    public string $uploadWidget;
    public ?UrlGenerator $urlGenerator = null;

    public function init()
    {
        parent::init();
        $this->urlGenerator = \Yii::createObject(UrlGenerator::class);
    }

    public function run(): string
    {
        return $this->render('list', [
            'files' => $this->files,
            'uploadWidget' => $this->uploadWidget,
            'urlGenerator' => $this->urlGenerator,
        ]);
    }

    public static function byLead(int $id, bool $withUpload): string
    {
        return self::widget([
            'files' => FileLeadQuery::getListByLead($id),
            'uploadWidget' => $withUpload ?  FileStorageUploadWidget::byLead($id) : '',
        ]);
    }

    public static function byCase(int $id, bool $withUpload): string
    {
        return self::widget([
            'files' => FileCaseQuery::getListByCase($id),
            'uploadWidget' => $withUpload ? FileStorageUploadWidget::byCase($id) : '',
        ]);
    }
}

<?php

namespace modules\fileStorage\src\widgets;

use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\entity\fileLead\FileLead;
use modules\fileStorage\src\UrlGenerator;
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
        if (!FileStorageSettings::isEnabled()) {
            return '';
        }
        return $this->render('list', [
            'files' => $this->files,
            'uploadWidget' => $this->uploadWidget,
            'urlGenerator' => $this->urlGenerator,
        ]);
    }

    public static function byLead(int $id): string
    {
        $files = FileLead::find()
            ->select(['fs_name', 'fs_path', 'fs_title', 'fld_fs_id'])
            ->byLead($id)
            ->innerJoinWith('file', false)
            ->orderBy(['fld_fs_id' => SORT_DESC])
            ->asArray()
            ->all();

        return self::widget([
            'files' => $files,
            'uploadWidget' => FileStorageUploadWidget::byLead($id),
        ]);
    }
}

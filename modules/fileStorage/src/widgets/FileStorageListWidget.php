<?php

namespace modules\fileStorage\src\widgets;

use modules\fileStorage\src\entity\fileLead\FileLead;
use yii\base\Widget;

/**
 * Class FileStorageListWidget
 *
 * @property $files
 */
class FileStorageListWidget extends Widget
{
    public array $files = [];
    public string $uploadWidget;

    public function init()
    {
        parent::init();
    }

    public function run(): string
    {
        return $this->render('list', ['files' => $this->files, 'uploadWidget' => $this->uploadWidget]);
    }

    public static function byLead(int $id): string
    {
        $files = FileLead::find()
            ->select(['fs_name as name', 'fs_path as url', 'fs_title as title', 'fld_fs_id'])
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

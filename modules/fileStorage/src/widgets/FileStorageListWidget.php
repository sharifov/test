<?php

namespace modules\fileStorage\src\widgets;

use modules\fileStorage\FileStorageSettings;
use modules\fileStorage\src\entity\fileCase\FileCaseQuery;
use modules\fileStorage\src\entity\fileLead\FileLeadQuery;
use modules\fileStorage\src\services\url\QueryParams;
use modules\fileStorage\src\services\url\UrlGenerator;
use src\auth\Auth;
use yii\base\Widget;
use modules\fileStorage\src\entity\fileStorage\FileStorageQuery;
use modules\fileStorage\src\services\access\FileStorageAccessService;

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
    public QueryParams $queryParams;
    public $canDelete = null;

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
            'queryParams' => $this->queryParams,
            'canView' => FileStorageSettings::canDownload() && Auth::can('file-storage/view'),
            'canDelete' => $this->canDelete ?? FileStorageAccessService::canDeleteFile(),
        ]);
    }

    public static function byLead(int $id, bool $withUpload): string
    {
        return self::widget([
            'files' => FileLeadQuery::getListByLead($id),
            'uploadWidget' => $withUpload ?  FileStorageUploadWidget::byLead($id) : '',
            'queryParams' => QueryParams::byLead(),
        ]);
    }

    public static function byCase(int $id, bool $withUpload): string
    {
        return self::widget([
            'files' => FileCaseQuery::getListByCase($id),
            'uploadWidget' => $withUpload ? FileStorageUploadWidget::byCase($id) : '',
            'queryParams' => QueryParams::byCase(),
        ]);
    }

    public static function byEmail(int $id, ?array $emailData): string
    {
        $files = [];
        if (isset($emailData['files']) && !empty($emailData['files'])) {
            foreach ($emailData['files'] as $val) {
                $file = (isset($val['uid'])) ? FileStorageQuery::getOneByUid($val['uid']) : FileStorageQuery::getOneByPath($val['value']);
                if (!empty($file)) {
                    $files[] = $file;
                }
            }
        }

        return self::widget([
            'files' => $files,
            'uploadWidget' => '',
            'queryParams' => QueryParams::byEmpty(),
            'canDelete' => false,
        ]);
    }
}

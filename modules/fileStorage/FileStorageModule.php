<?php

namespace modules\fileStorage;

use yii\base\Module;

class FileStorageModule extends Module
{
    public $controllerNamespace = 'modules\fileStorage\controllers';

    public function init(): void
    {
        parent::init();

        $this->setViewPath('@modules/fileStorage/views');
    }

    public static function getMimeTypes(): array
    {
        return [
            'image/jpeg' => 'image/jpeg',
            'image/png' => 'image/png',
            'text/plain' => 'text/plain',
            'application/pdf' => 'application/pdf',
            'application/msword' => 'application/msword',
        ];
    }

    public static function getListMenu(string $modulePath = 'file-storage'): array
    {
        $items = [
            ['label' => 'Files', 'url' => ['/' . $modulePath . '/file-storage/index']],
            ['label' => 'Share', 'url' => ['/' . $modulePath . '/file-share/index']],
            ['label' => 'Log', 'url' => ['/' . $modulePath . '/file-log/index']],
            ['label' => 'Leads', 'url' => ['/' . $modulePath . '/file-lead/index']],
            ['label' => 'Cases', 'url' => ['/' . $modulePath . '/file-case/index']],
            ['label' => 'Clients', 'url' => ['/' . $modulePath . '/file-client/index']],
            ['label' => 'Users', 'url' => ['/' . $modulePath . '/file-user/index']],
        ];
        return $items;
    }
}

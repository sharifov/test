<?php

namespace modules\fileStorage\src\validator;

use modules\fileStorage\FileStorageSettings;

class FileValidator extends \yii\validators\FileValidator
{
    public function init()
    {
        $this->maxSize = FileStorageSettings::getUploadMaxSize();
        $this->mimeTypes = array_keys(FileStorageSettings::getMimeTypes());
        $this->extensions = array_keys(FileStorageSettings::getAllowExt());
        $this->checkExtensionByMimeType = false;
        parent::init();
    }
}

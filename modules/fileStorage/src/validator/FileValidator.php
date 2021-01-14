<?php

namespace modules\fileStorage\src\validator;

use modules\fileStorage\FileStorageModule;

class FileValidator extends \yii\validators\FileValidator
{
    public function init()
    {
        $this->maxSize = FileStorageModule::getUploadMaxSize();
        $this->mimeTypes = array_keys(FileStorageModule::getMimeTypes());
        parent::init();
    }
}

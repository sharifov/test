<?php


namespace frontend\assets\overridden;

class DosamigosCKEditorAsset extends \dosamigos\ckeditor\CKEditorAsset
{
    public function init()
    {
        parent::init();
        $this->js[] = 'config.js';
        $this->js[] = 'lang/en.js';
        $this->js[] = 'styles.js';
    }
}

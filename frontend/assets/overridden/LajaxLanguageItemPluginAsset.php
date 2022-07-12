<?php


namespace frontend\assets\overridden;

use common\models\Language;
use lajax\translatemanager\Module;
use lajax\translatemanager\services\Generator;

class LajaxLanguageItemPluginAsset extends \yii\web\AssetBundle
{
    public $sourcePath = "@frontend/web/js/translate/";

    public $js = ['translate/lang.js'];

    public function init()
    {
        parent::init();
        $app = \Yii::$app->id;

        if ($app === 'app-console') {
            /** @var $translateModule Module */
            $translateModule =\Yii::$app->getModule('translatemanager');
            $translateModule->tmpDir = $this->sourcePath;

            $language = Language::findOne(['status' => 1]);

            if (!$language) {
                throw new \RuntimeException('Language with status 1 is not found');
            }

            $tmpFilePath = \Yii::getAlias($this->sourcePath);
            if (!file_exists(\Yii::getAlias($this->sourcePath))) {
                if (!mkdir($tmpFilePath) && !is_dir($tmpFilePath)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $tmpFilePath));
                }
            }

            $generator = new Generator($translateModule, 'lang');
            $generator->run();
        }
    }
}

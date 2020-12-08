<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella_v2\assets;

use sales\helpers\setting\SettingHelper;

class SentryAsset extends \yii\web\AssetBundle
{
//    public $sourcePath = '@frontend/themes/gentelella_v2/';
//    public $baseUrl = '@web';

    public function init()
    {
        parent::init();
        if (SettingHelper::isSentryFrontendEnabled()) {
            $this->js[] = [
                'https://js.sentry-cdn.com/759a9e865aaa4088acd6fb21376c5289.min.js',
                'crossorigin' => 'anonymous',
                'data-lazy' => 'no',
                'position' => \yii\web\View::POS_HEAD,
            ];
        }
    }
}

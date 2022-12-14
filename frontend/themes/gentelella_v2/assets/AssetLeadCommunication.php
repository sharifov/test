<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace frontend\themes\gentelella_v2\assets;

use frontend\assets\groups\AllSharedAsset;
use frontend\themes\gentelella_v2\assets\groups\GentelellaAsset;
use yii\web\JqueryAsset;

class AssetLeadCommunication extends \yii\web\AssetBundle
{
    //public $sourcePath = '@frontend/themes/gentelella/';
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        // 'css/style-lead-communication.css',
    ];

    public $js = [
        'https://cdnjs.cloudflare.com/ajax/libs/scrollup/2.4.1/jquery.scrollUp.min.js',
        '/js/sms_counter.min.js',
//		'https://cdnjs.cloudflare.com/ajax/libs/timer.jquery/0.9.0/timer.jquery.min.js'
    ];

    public $depends = [
        JqueryAsset::class
    ];
}

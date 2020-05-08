<?php
/**
 * @copyright Copyright (c) 2020 Kivork
 */

namespace frontend\widgets\centrifugo;

use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * @author vincent.barnes
 * @since 2.20
 */
class CentrifugoNotificationAssets extends AssetBundle
{
    public $sourcePath = ('@frontend/widgets/centrifugo/assets/');

    public $css = [
        'css/cent-notification.css'
    ];
    public $js = [
        'js/cent-notification.js'
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END,
    ];

    public $depends = [
        YiiAsset::class,
    ];

    public $publishOptions = [
        'forceCopy' => false,
    ];

    public function init()
    {
        parent::init();
    }
}
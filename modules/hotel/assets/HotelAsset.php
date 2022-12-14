<?php

namespace modules\hotel\assets;

use yii\web\AssetBundle;

/**
 * Class UserAsset
 * @package modules\hotel\assets
 */
class HotelAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@modules/hotel/assets/src/';

    /**
     * @var array
     */
    public $css = [
        'css/search_quote.css'
    ];

    /**
     * @var array
     */
    public $js = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
//        $this->sourcePath = __DIR__ . '/src';
//        $this->js = ['js/user.js'];
    }

    /**
     * @var array
     */
    public $publishOptions = [
        'forceCopy' => false
    ];

    /**
     * @var array
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
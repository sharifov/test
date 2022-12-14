<?php

namespace modules\attraction\assets;

use yii\web\AssetBundle;

/**
 * Class AttractionAsset
 * @package modules\attraction\assets
 */
class AttractionAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '';

    /**
     * @var array
     */
    public $css = [];

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
        $this->sourcePath = __DIR__ . '/src';
        $this->js = ['js/user.js'];
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
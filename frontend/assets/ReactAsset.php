<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * React application asset bundle.
 *
 * @author Alex Connor <alex.connor@techork.com>
 * @since 1.0
 */
class ReactAsset extends AssetBundle
{
    public $basePath = null;
    public $sourcePath = null;
    public $baseUrl = '@web';

    public $js = [
        ['https://unpkg.com/react@16.14.0/umd/react.development.js', 'position' => \yii\web\View::POS_HEAD, 'crossorigin' => true],
        ['https://unpkg.com/react-dom@16.14.0/umd/react-dom.development.js', 'position' => \yii\web\View::POS_HEAD, 'crossorigin' => true],
        ['https://unpkg.com/babel-standalone@6.26.0/babel.min.js'],
    ];
}

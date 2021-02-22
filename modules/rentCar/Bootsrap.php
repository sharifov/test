<?php

namespace modules\rentCar;

use Yii;
use yii\i18n\PhpMessageSource;

/**
 * Class Bootstrap
 */
class Bootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['modules/rentCar/*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@modules/rentCar/messages',
            'fileMap' => [
                'modules/rentCar/module' => 'module.php',
            ]
        ];
        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules(
            [
                '' => 'rent-car/default/index',
                '<_a:(act1|act2)>' => 'rent-car/default/<_a>'
            ]
        );
    }
}

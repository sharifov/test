<?php

namespace modules\attraction;

use Yii;
use yii\i18n\PhpMessageSource;

/**
 * Class Bootstrap
 * @package modules\attraction
 */
class Bootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['modules/attraction/*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@modules/attraction/messages',
            'fileMap' => [
                'modules/attraction/module' => 'module.php',
            ]
        ];
        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules(
            [
                // Declaration of rules here
                '' => 'attraction/default/index',
                '<_a:(act1|act2)>' => 'attraction/default/<_a>'
            ]
        );
    }
}

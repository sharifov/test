<?php
namespace modules\hotel;
use Yii;
use yii\i18n\PhpMessageSource;
/**
 * Class Bootstrap
 * @package modules\main
 */
class Bootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['modules/hotel/*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@modules/hotel/messages',
            'fileMap' => [
                'modules/hotel/module' => 'module.php',
                'modules/hotel/frontend' => 'frontend.php'
            ]
        ];
        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules(
            [
                // Declaration of rules here
                '' => 'main/default/index',
                '<_a:(about|contact|captcha)>' => 'main/default/<_a>'
            ]
        );
    }
}
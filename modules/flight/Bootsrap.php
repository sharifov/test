<?php
namespace modules\flight;
use Yii;
use yii\i18n\PhpMessageSource;
/**
 * Class Bootstrap
 * @package modules\flight
 */
class Bootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $i18n = Yii::$app->i18n;
        $i18n->translations['modules/flight/*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => '@modules/flight/messages',
            'fileMap' => [
                'modules/flight/module' => 'module.php',
            ]
        ];
        $urlManager = Yii::$app->urlManager;
        $urlManager->addRules(
            [
                // Declaration of rules here
                '' => 'flight/default/index',
                '<_a:(act1|act2)>' => 'flight/default/<_a>'
            ]
        );
    }
}
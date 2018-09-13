<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\components\Helper;
//use common\widgets\Alert;

//use webvimark\modules\UserManagement\UserManagementModule;

AppAsset::register($this);

//$bundle = backend\themes\gentelella\assets\Asset::register($this);

$bundle = yiister\gentelella\assets\Asset::register($this);

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?
    $this->registerMetaTag(['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8']);
    $this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);

    $this->registerMetaTag(['charset' => Yii::$app->charset]);
    $this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
    $this->metaTags[] = Html::csrfMetaTags();
    $this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
    $this->registerMetaTag(['name' => 'msapplication-TileColor', 'content' => '#a9e04b']);
    //$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Helper::publishStatic('images/favicons/32x32.png'), 'sizes' => '32x32']);
    //$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Helper::publishStatic('images/favicons/16x16.png'), 'sizes' => '16x16']);
    $this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/png', 'href' => Helper::publishStatic('images/favicons/16x16.png')]);
    $this->head();
    //$this->head();

    $host = 'Error - ' . $_SERVER['HTTP_HOST']; //str_replace(['http://','https://', '.photolamus.com'], '',$_SERVER['HTTP_HOST']);
    echo Html::tag('title', ucfirst($host).' - '.Html::encode($this->title));
    ?>
    <?php $this->head() ?>
</head>
<body class="login">
<?php $this->beginBody(); ?>
    <div class="row">
    <?= $content ?>
    </div>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>

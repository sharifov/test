<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
$bundle = yiister\gentelella\assets\Asset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="description" content="">
    <?
    $this->registerMetaTag(['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8']);
    $this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);

    $this->registerMetaTag(['charset' => Yii::$app->charset]);
    $this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
    $this->metaTags[] = Html::csrfMetaTags();
    $this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
    //$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Helper::publishStatic('images/favicons/16x16.png'), 'sizes' => '16x16']);
    $this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl.'/favicon.ico']);
    $this->head();

    //$this->head();

    $host = str_replace(['http://','https://'], '', $_SERVER['HTTP_HOST']);
    echo Html::tag('title', ucfirst($host). ' - AUTHORIZATION');
    ?>
    <?php /*<link rel="stylesheet" href="<?= Yii::$app->getAssetManager()->publish(Yii::getAlias('@frontend').'/web/css/style_theme.css')[1];?>"/> ?>
    <?php //php $this->head() ?>
    <?php /*<link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />*/ ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>


    <![endif]-->
</head>
<body class="login">
<?php $this->beginBody(); ?>
    <?= $content ?>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
$bundle = yiister\gentelella_v2\assets\Asset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?php
        $this->registerMetaTag(['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8']);
        $this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);
        $this->registerMetaTag(['charset' => Yii::$app->charset]);
        $this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
        $this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
        $this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl.'/error.ico']);
        $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl.'/error.ico']);
        $this->metaTags[] = Html::csrfMetaTags();
        $this->head();
        echo Html::tag('title', Html::encode($this->title));
    ?>
</head>
<body class="nav-md">
<?php $this->beginBody(); ?>
<div class="container body">
    <div class="main_container">
        <div class="row">
            <?= $content ?>
        </div>
    </div>
</div>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>

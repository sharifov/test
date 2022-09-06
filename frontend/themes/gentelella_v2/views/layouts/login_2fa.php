<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

\frontend\themes\gentelella_v2\assets\SimpleAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="description" content="">
    <?php
    $this->registerMetaTag(['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8']);
    $this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);

    $this->registerMetaTag(['charset' => Yii::$app->charset]);
    $this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
    $this->metaTags[] = Html::csrfMetaTags();
    $this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
    $this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl . '/favicon.ico']);
    $this->head();

    $host = str_replace(['http://','https://'], '', $_SERVER['HTTP_HOST']);
    echo Html::tag('title', ucfirst($host) . ' - AUTHORIZATION');
    ?>
</head>
<body class="login">

<?php
$this->beginBody();
echo $content;
$this->endBody();
?>

<?php
$css = <<<CSS
#two-factor-col {
    margin-left: 33.3%!important;
}

#two-factor-header {
    padding: 5px 10px;
    background-color: transparent;
}

#two-factor-header > * {
    padding: 5px 10px;
    margin: 0;
    text-align: center;
}

#two-factor-body a.btn {
    padding: 3px 7px;
}
CSS;

$this->registerCss($css);
?>

</body>
</html>
<?php $this->endPage(); ?>

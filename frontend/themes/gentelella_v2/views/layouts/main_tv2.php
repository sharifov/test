<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\widgets\OnlineConnection;
use src\auth\Auth;
use yii\helpers\Html;

//\frontend\themes\gentelella_v2\assets\Asset::register($this);
$bundle = \frontend\themes\gentelella_v2\assets\SimpleAsset::register($this);

//\frontend\assets\NotifyAsset::register($this);


?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="description" content="Sales">
    <?php
        $this->registerMetaTag(['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8']);
        $this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);

        $this->registerMetaTag(['charset' => Yii::$app->charset]);
        $this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
        $this->metaTags[] = Html::csrfMetaTags();
        $this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
        $this->registerMetaTag(['name' => 'msapplication-TileColor', 'content' => '#a9e04b']);
        //$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Helper::publishStatic('images/favicons/16x16.png'), 'sizes' => '16x16']);
        $this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl . '/favicon.phone.ico']);
        $this->head();
        // $host = 'Sales';
        echo Html::tag('title', Html::encode($this->title));
    ?>
</head>
<body>
<?php $this->beginBody(); ?>
<div class="body">
    <div class="main_container">
        <!-- top navigation -->
        <div class="container-fluid">

            <nav class="d-flex justify-content-between top_nav" role="navigation" style="margin-left: 0;">
                <ul class="nav navbar-nav navbar-left">
                    <li>
    <!--                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">-->
                            <?= frontend\widgets\UserInfoWidget::widget() ?>
    <!--                    </a>-->
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right d-flex flex-row">
                    <li class="">
                        <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            <?php /*<img src="http://placehold.it/128x128" alt="">*/ ?>

                            <?php //=Html::img($grav_url, ['alt' => 'avatar'])?>

                            Menu

<!--                            <span class=" fa fa-angle-down"></span>-->
                        </a>
                        <ul class="dropdown-menu dropdown-usermenu pull-right">
                            <?php /*<li><a href="javascript:;">  Profile</a>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <span class="badge bg-red pull-right">50%</span>
                                    <span>Settings</span>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;">Help</a>
                            </li>*/ ?>

                            <li>
                                <?=Html::a(
                                    '<i class="fa fa-home pull-right"></i> Home',
                                    ['/site/index'],
                                    ['title' => 'Home']
                                ) ?>
                                <?php /*=Html::a('<i class="fa fa-user pull-right"></i> My Profile', ['/site/profile'],
                                    ['title' => "My Profile"])*/ ?>
                                <?php /*=Html::a('<i class="fa fa-sign-out pull-right"></i> Log Out', ['/user-management/auth/logout'],
                                    ['title' => "Logout"])*/ ?>
                            </li>
                            <li>
                                <?=Html::a(
                                    '<i class="fa fa-sign-out pull-right"></i> Log Out',
                                    ['/site/logout'],
                                    ['title' => 'Logout']
                                ) ?>
                            </li>
                        </ul>
                    </li>
                    <?php //= frontend\widgets\Notifications::widget();?>
                    <?= frontend\widgets\OnlineConnection::widget() ?>

                    <li>
                        <a href="javascript:;" class="info-number" title="Incoming Call - Volume ON" id="incoming-sound-indicator">
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
        <!-- /top navigation -->
        <div class="container-fluid">
            <div class="row">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\models\Employee;
use frontend\widgets\frontendWidgetList\FrontendLauncherWidgetList;
use frontend\widgets\frontendWidgetList\louassist\LouAssistWidget;
use frontend\widgets\frontendWidgetList\userflow\UserFlowWidget;
use frontend\widgets\notification\NotificationSocketWidget;
use frontend\widgets\notification\NotificationWidget;
use src\auth\Auth;
use src\helpers\app\ReleaseVersionHelper;
use yii\helpers\Html;

$bundle = \frontend\assets\AppCrudAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="description" content="CRM Sales">
    <?php
        $this->registerMetaTag(['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8']);
        $this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);
        $this->registerMetaTag(['charset' => Yii::$app->charset]);
        $this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
        $this->metaTags[] = Html::csrfMetaTags();
        $this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
        $this->registerMetaTag(['name' => 'msapplication-TileColor', 'content' => '#a9e04b']);
        $this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl . '/favicon.ico']);
        $this->head();
        $host = 'CRM';
        echo Html::tag('title', ucfirst($host) . ' - ' . Html::encode($this->title));
    ?>
</head>
<body class="nav-<?= !empty($_COOKIE['menuIsCollapsed']) && $_COOKIE['menuIsCollapsed'] === 'true' ? 'sm' : 'md' ?>">
<?php $this->beginBody(); ?>

<div id="page-loader" class="overlay" style="display: block">
    <div class="preloader">
        <span class="fas fa-circle-o-notch fa-spin fa-10x"></span>
        <div class="preloader__text">Loading ...</div>
    </div>
</div>

<div class="container body">
    <div class="main_container">
        <?php if (!Yii::$app->user->isGuest) :?>
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">

                <?php
                    /** @var Employee $user */
                    $user = Yii::$app->user->identity;
                    $gravUrl = $user->getGravatarUrl();
                ?>

                <!-- navbar left -->
                <?= $this->render('_navbar_left', ['host' => $host, 'grav_url' => $gravUrl]) ?>
                <!-- /navbar left -->

                <div class="grav-img-sm">
                    <?=Html::img($gravUrl, ['alt' => 'avatar', 'class' => 'img-circle profile_img', 'title' => $user->full_name])?>
                </div>

                <!-- sidebar menu -->
                <?= \frontend\themes\gentelella_v2\widgets\SideBarMenu::widget(['user' => $user]); ?>
                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <?= $this->render('_sidebar_footer') ?>
                <!-- /menu footer buttons -->
            </div>
        </div>


        <!-- top navigation -->
        <div class="top_nav">

            <div class="nav_menu">
                <div class="nav toggle">
                    <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                </div>

                <nav class="nav navbar-nav">
                    <ul class="navbar-right">
                        <li class="nav-item dropdown open">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">

                                <?=Html::img($gravUrl, ['alt' => 'avatar'])?>
                                <span>
                                    <b><?=implode(', ', $user->getRoles()) ?></b>:
                                </span>
                                <?=Html::encode($user->username)?>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li>
                                    <?=Html::a(
                                        '<i class="fa fa-user pull-right"></i> My Profile',
                                        ['/site/profile'],
                                        ['title' => 'My Profile']
                                    ) ?>
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
                        <?php echo frontend\widgets\OnlineConnection::widget() ?>
                        <?php echo frontend\widgets\UserMonitor::widget() ?>

                        <?php echo (new FrontendLauncherWidgetList())->getContent() ?>


                        <?php
                        if (Yii::$app->params['settings']['notification_web_socket']) {
                            echo NotificationSocketWidget::widget(['userId' => Auth::id()]);
                        } else {
                            echo NotificationWidget::widget(['userId' => Auth::id()]);
                        }
                        ?>

                    </ul>
                </nav>
            </div>

        </div>
        <!-- /top navigation -->
        <?php endif;?>

        <!-- page content -->
        <div class="right_col" role="main">

            <div id="desktop-phone-notifications">
            </div>

            <?php if (isset($this->params['h1'])) : ?>
                <div class="page-title">
                    <div class="title_left">
                        <h1><?= $this->params['h1'] ?></h1>
                    </div>
                    <div class="title_right">
                        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search for...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">Go!</button>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12">
                    <?php echo \yii\bootstrap4\Breadcrumbs::widget([
                        'homeLink' => [
                            'label' => false,
                            'template' => '<li class="breadcrumb-item"><a href="' . Yii::$app->urlManager->createUrl('/') . '">Home</a></li>',
                        ],
                        'links' => $this->params['breadcrumbs'] ?? [],
                    ]); ?>
                </div>

                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="spinner-wrapper">
                        <div class="spinner">
                        </div>
                    </div>
                    <?= \frontend\themes\gentelella_v2\widgets\FlashAlert::widget() ?>
                    <?= $content ?>
                </div>
            </div>
        </div>
        </div>
        <!-- /page content -->
        <?= $this->render('_footer') ?>
    </div>
</div>
<!-- /footer content -->

<!-- modals -->
<?= $this->render('_modals') ?>
<!-- /modals -->

<div id="preloader" class="overlay d-none">
    <div class="preloader">
        <span class="fa fa-spinner fa-pulse fa-3x fa-fw"></span>
        <div class="preloader__text">Loading...</div>
    </div>
</div>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
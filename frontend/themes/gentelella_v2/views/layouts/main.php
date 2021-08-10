<?php

/* @var $this \yii\web\View */
/* @var $content string */

use common\models\Employee;
use frontend\themes\gentelella_v2\assets\groups\GentelellaAsset;
use frontend\widgets\clientChat\ClientChatAccessWidget;
use frontend\widgets\frontendWidgetList\FrontendLauncherWidgetList;
use frontend\widgets\frontendWidgetList\louassist\LouAssistWidget;
use frontend\widgets\notification\NotificationSocketWidget;
use frontend\widgets\notification\NotificationWidget;
use sales\auth\Auth;
use sales\helpers\app\ReleaseVersionHelper;
use yii\helpers\Html;
use sales\helpers\setting\SettingHelper;

//$bundle = \frontend\themes\gentelella_v2\assets\Asset::register($this);
//$bundle = \frontend\assets\AppAsset::register($this);
$bundle = \frontend\assets\AppAsset::register($this);


//$this->registerCssFile('@frontend/themes/gentelella/css/custom.css');
//Yii::$app->view->registerCssFile('@frontend/themes/gentelella/css/custom.css', ['depends'=>'yiister\gentelella_v2\assets\Asset']);

//\frontend\assets\groups\Gentellella::register($this);
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
        //$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Helper::publishStatic('images/favicons/16x16.png'), 'sizes' => '16x16']);
        $this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl . '/favicon.ico']);
        $this->head();

        //$this->head();

        $host = 'CRM';
        echo Html::tag('title', ucfirst($host) . ' - ' . Html::encode($this->title));
    ?>
    <?php /*<link rel="stylesheet" href="<?= Yii::$app->getAssetManager()->publish(Yii::getAlias('@frontend').'/web/css/style_theme.css')[1];?>"/>*/ ?>
    <?php //php $this->head()?>
    <?php /*<link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />*/ ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <?php
    if (SettingHelper::isSentryFrontendEnabled()) {
        $options = [
            'releaseVer' => Yii::$app->params['release']['version'] ?? '',
            'environment' => YII_ENV,
            'username' => Auth::user()->username ?? '',
        ];
        $this->registerJs(
            "let extraInfo = " . \yii\helpers\Json::htmlEncode($options) . ";",
            \yii\web\View::POS_HEAD,
            'extraInfo'
        );
        $sentryJS = <<<JS
            Sentry.onLoad(function() {
                Sentry.init({
                    debug: false,
                    release: extraInfo.releaseVer,
                    environment: extraInfo.environment,
                });
                Sentry.setUser({ username: extraInfo.username });
            });
        JS;
        $this->registerJs($sentryJS, \yii\web\View::POS_HEAD);
    }
    ?>

</head>
<body class="nav-<?= !empty($_COOKIE['menuIsCollapsed']) && $_COOKIE['menuIsCollapsed'] === 'true' ? 'sm' : 'md' ?>">
<?php $this->beginBody(); ?>
<?= \frontend\widgets\YandexMetrikaWidget::widget(); ?>

<div id="page-loader" class="overlay" style="display: block">
    <div class="preloader">
        <span class="fas fa-circle-o-notch fa-spin fa-10x"></span>
        <div class="preloader__text">Loading ...<br>"<?= Html::encode($this->title)?>"</div>
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
                <?php //= $this->render('_sidebar_menu')?>
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

                        <?php /*php if($isAdmin):*/ ?>

                            <?= frontend\widgets\OnlineConnection::widget() ?>
                            <?= frontend\widgets\UserMonitor::widget() ?>

                            <?php echo (new FrontendLauncherWidgetList())->getContent() ?>

                            <?php //= frontend\widgets\Notifications::widget()?>
                            <?php
                            if (Yii::$app->params['settings']['notification_web_socket']) {
                                echo NotificationSocketWidget::widget(['userId' => Auth::id()]);
                            } else {
                                echo NotificationWidget::widget(['userId' => Auth::id()]);
                            }
                            ?>
                        <?php /*= CentrifugoNotificationWidget::widget([
                            'userId' => Auth::id(),
                            'widgetView => 'index',
                            'userAllowedChannels' => [
                                'ownUserChannel#' . Auth::id(),
                                'multipleUsersChannel#658,659'
                            ]
                        ]) */ ?>

                        <li class="nav-item">
                            <a href="javascript:;" class="info-number" data-status="1" title="Incoming Call - Volume ON" id="incoming-sound-indicator"><i class="fa fa-volume-up text-success"> </i></a>
                        </li>

                        <?php /*php endif;*/?>

                        <?php //= frontend\widgets\ChatNotifications::widget();?>

                    </ul>
                </nav>
            </div>

        </div>
        <!-- /top navigation -->
        <?php endif;?>

        <!-- page content -->
        <div class="right_col" role="main">

            <div id="desktop-phone-notifications">
<!--                <div class="phone-notifications">-->
<!--                    <ul class="phone-notifications__list">-->
<!--                        <li class="phone-notifications__item phone-notifications__item--shown">-->
<!--                            <div class="incoming-notification">-->
<!--                                <i class="user-icn">G</i>-->
<!--                                <div class="incoming-notification__inner">-->
<!--                                    <div class="incoming-notification__info">-->
<!--                                        <div class="incoming-notification__general-info">-->
<!--                                            <b class="incoming-notification__name">Geffy Morgan Jefferson</b>-->
<!--                                            <span class="incoming-notification__phone">+1 (888) 88 888 88</span>-->
<!--                                            <div class="incoming-notification__project">-->
<!--                                                <b class="incoming-notification__project-name">WOWFARE</b>-->
<!--                                                <i>•</i>-->
<!--                                                <span class="incoming-notification__position">Sales General</span>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="incoming-notification__timer">-->
<!--                                            <span>24:32</span>-->
<!--                                        </div>-->
<!---->
<!--                                    </div>-->
<!--                                    <div class="incoming-notification__action-list">-->
<!--                                        <div class="incoming-notification__dynamic">-->
<!--                                            <a href="#" class="incoming-notification__action incoming-notification__action--line">-->
<!--                                                <i class="fa fa-random"></i>-->
<!--                                            </a>-->
<!---->
<!--                                            <a href="#" class="incoming-notification__action incoming-notification__action--info">-->
<!--                                                <i class="fa fa-info"></i>-->
<!--                                            </a>-->
<!---->
<!--                                            <a href="#" class="incoming-notification__action incoming-notification__action--phone">-->
<!--                                                <i class="fa fa-phone"></i>-->
<!--                                            </a>-->
<!--                                        </div>-->
<!--                                        <a href="#" class="incoming-notification__action incoming-notification__action--max">-->
<!--                                            <i class="fa fa-long-arrow-down"></i>-->
<!--                                        </a>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </li>-->
<!--                    </ul>-->
<!--                </div>-->
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
        <!-- footer content -->
        <footer>
            <p class="pull-left">&copy; <?=Yii::$app->name ?> <?= date('Y') ?>,
                <span title="<?=Yii::$app->params['release']['git_branch'] ?? ''?> : <?=Yii::$app->params['release']['git_hash'] ?? ''?>">
                    v. <?php echo ReleaseVersionHelper::getReleaseVersion(true) ?? '' ?>
                </span>
                <span title="Hostname">
                    , host: <?=Yii::$app->params['appHostname'] ?? ''?>
                </span>
            </p>
            <p class="pull-right"><small><i><?=date('Y-m-d H:i:s')?></i></small></p>

            <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
    </div>
</div>
<?php /*<div id="custom_notifications" class="custom-notifications dsp_none">
    <ul class="list-unstyled notifications clearfix" data-tabbed_notifications="notif-group">
    </ul>
    <div class="clearfix"></div>
    <div id="notif-group" class="tabbed_notifications"></div>
</div>*/ ?>
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
<?= frontend\widgets\WebPhone::widget() ?>
<?php if (Auth::can('PhoneWidget')) : ?>
    <?= frontend\widgets\NewWebPhoneWidget::widget(['userId' => Auth::id()]) ?>
<?php endif; ?>

<div id="_client_chat_access_widget">
    <?= ClientChatAccessWidget::widget(['userId' => Auth::id()]) ?>
</div>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
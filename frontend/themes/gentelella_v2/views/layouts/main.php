<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

$bundle = \frontend\themes\gentelella_v2\assets\Asset::register($this);


//$this->registerCssFile('@frontend/themes/gentelella/css/custom.css');
//Yii::$app->view->registerCssFile('@frontend/themes/gentelella/css/custom.css', ['depends'=>'yiister\gentelella_v2\assets\Asset']);

\frontend\assets\NotifyAsset::register($this);

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
    $this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl.'/favicon.ico']);
    $this->head();

    //$this->head();

    $host = 'Sales';
    echo Html::tag('title', ucfirst($host).' - '.Html::encode($this->title));
    ?>
    <?php /*<link rel="stylesheet" href="<?= Yii::$app->getAssetManager()->publish(Yii::getAlias('@frontend').'/web/css/style_theme.css')[1];?>"/>*/ ?>
    <?php //php $this->head() ?>
    <?php /*<link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />*/ ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="nav-<?= !empty($_COOKIE['menuIsCollapsed']) && $_COOKIE['menuIsCollapsed'] === 'true' ? 'sm' : 'md' ?>">
<?php $this->beginBody(); ?>
<div class="container body">
    <div class="main_container">
        <?php if(!Yii::$app->user->isGuest):?>

        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">

                <?php
                    /** @var \common\models\Employee $user */
                    $user = Yii::$app->user->identity;

                    $default = 'identicon';

                    if($user && $user->email) {
                        $gravUrl = '//www.gravatar.com/avatar/' . md5(strtolower(trim($user->email))) . '?d=identicon&s=128';

                    } else {
                        $gravUrl = '//www.gravatar.com/avatar/?d=identicon&s=60';
                    }

                ?>

                <!-- navbar left -->
                <?= $this->render('_navbar_left', ['host' => $host, 'grav_url' => $gravUrl]) ?>
                <!-- /navbar left -->

                <div class="grav-img-sm">
					<?=Html::img($gravUrl, ['alt' => 'avatar', 'class' => 'img-circle profile_img', 'title' => $user->full_name])?>
                </div>

                <!-- sidebar menu -->
                <?= $this->render('_sidebar_menu') ?>
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
                                    <?=Html::a('<i class="fa fa-user pull-right"></i> My Profile', ['/site/profile'],
                                        ['title' => 'My Profile']) ?>
                                </li>
                                <li>
                                    <?=Html::a('<i class="fa fa-sign-out pull-right"></i> Log Out', ['/site/logout'],
                                        ['title' => 'Logout']) ?>
                                </li>
                            </ul>
                        </li>



                        <?php /*php if($isAdmin):*/ ?>
                            <?= frontend\widgets\OnlineConnection::widget() ?>
                            <?= frontend\widgets\Notifications::widget() ?>


                        <li class="nav-item">
                            <a href="javascript:;" class="info-number" title="Incoming Call - Volume ON" id="incoming-sound-indicator"></a>
                        </li>

                        <?php /*php endif;*/?>

                        <?php //= frontend\widgets\ChatNotifications::widget(); ?>

                    </ul>
                </nav>
            </div>

        </div>
        <!-- /top navigation -->
        <?php endif;?>

        <!-- page content -->
        <div class="right_col" role="main">
            <?php if (isset($this->params['h1'])): ?>
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
                            'template' => '<li class="breadcrumb-item"><a href="'.Yii::$app->urlManager->createUrl('/').'">Home</a></li>',
                        ],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]);?>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <?=\frontend\themes\gentelella_v2\widgets\FlashAlert::widget()?>
                    <?= $content ?>
                </div>
            </div>
        </div>
        <!-- /page content -->
        <!-- footer content -->
        <footer>
            <p class="pull-left">&copy; <?=Yii::$app->name ?> <?= date('Y') ?></p>
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

<?= frontend\widgets\CallBox::widget() ?>
<?= frontend\widgets\WebPhone::widget() ?>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
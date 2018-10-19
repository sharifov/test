<?php

/* @var $this \yii\web\View */
/* @var $content string */


use yii\helpers\Html;
use \webvimark\modules\UserManagement\UserManagementModule;

//use common\widgets\Alert;

//use webvimark\modules\UserManagement\UserManagementModule;
//$bundle = yiister\gentelella\assets\Asset::register($this);

//\backend\assets\AppAsset::register($this);
$bundle = \frontend\themes\gentelella\assets\Asset::register($this);

//$this->registerCssFile('@backend/themes/gentelella/css/custom.css');
//Yii::$app->view->registerCssFile('@backend/themes/gentelella/css/custom.css', ['depends'=>'yiister\gentelella\assets\Asset']);

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="description" content="Book Air">
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

    $host = 'BookAir';
    echo Html::tag('title', ucfirst($host).' - '.Html::encode($this->title));
    ?>
    <link rel="stylesheet" href="<?= Yii::$app->getAssetManager()->publish(Yii::getAlias('@frontend').'/web/css/style_theme.css')[1];?>"/>
    <?//php $this->head() ?>
    <? /*<link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />*/ ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="nav-md">
<?php $this->beginBody(); ?>
<div class="container body">
    <div class="main_container">
        <?php if(!Yii::$app->user->isGuest):?>

        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">

                <?php
                    $me = \common\models\Employee::findOne(Yii::$app->user->id);
                    $default = "identicon";

                    if(!$me || !$me->email) {
                        $grav_url = '//www.gravatar.com/avatar/?d=identicon&s=60';
                    }
                    else {
                        $grav_url = "//www.gravatar.com/avatar/" . md5(strtolower(trim($me->email))) . "?d=identicon&s=128";
                    }
                ?>

                <!-- navbar left -->
                <?= $this->render('_navbar_left', ['host' => $host, 'grav_url' => $grav_url]) ?>
                <!-- /navbar left -->

                <!-- sidebar menu -->
                <?= $this->render('_sidebar_menu') ?>
                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <div class="sidebar-footer hidden-small">
                    <? /*<a data-toggle="tooltip" data-placement="top" title="Settings">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>*/ ?>

                    <?=Html::a('<span class="glyphicon glyphicon-off" aria-hidden="true"></span>', ['/user-management/auth/logout'],
                        ['data-toggle' => "tooltip", 'data-placement' => "top", 'title' => "Logout"]) ?>

                    <?/*=Html::a('<span class="fa fa-certificate" aria-hidden="true"></span>', ['site/index', 'snow' => 'off'],
                        ['data-toggle' => "tooltip", 'data-placement' => "top", 'title' => "Snow Off"])*/ ?>


                </div>
                <!-- /menu footer buttons -->
            </div>
        </div>


        <!-- top navigation -->
        <div class="top_nav">

            <div class="nav_menu">
                <nav class="" role="navigation">
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <? /*<img src="http://placehold.it/128x128" alt="">*/ ?>

                                <?=Html::img($grav_url, ['alt' => 'avatar'])?>

                                <?
                                    $myRolesModel = \webvimark\modules\UserManagement\models\rbacDB\Role::getUserRoles(Yii::$app->user->id);
                                    $myRoles = [];
                                    if($myRolesModel)
                                        foreach ($myRolesModel as $role) {
                                            if($role->name == 'guest') continue;
                                            $myRoles[] = $role->name;
                                        }

                                ?>
                                <b><?=implode(', ', $myRoles) ; ?></b>:
                                <?=Html::encode(Yii::$app->user->identity->username)?>

                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <? /*<li><a href="javascript:;">  Profile</a>
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
                                    <?=Html::a('<i class="fa fa-user pull-right"></i> My Profile', ['/site/profile'],
                                        ['title' => "My Profile"]) ?>
                                    <?=Html::a('<i class="fa fa-sign-out pull-right"></i> Log Out', ['/site/logout'],
                                        ['title' => "Logout"]) ?>
                                    <?php /*=Html::a('<i class="fa fa-sign-out pull-right"></i> Log Out', ['/user-management/auth/logout'],
                                        ['title' => "Logout"])*/ ?>

                                </li>
                            </ul>
                        </li>
                        <?//= backend\widgets\Notifications::widget(); ?>

                        <?//= backend\widgets\ChatNotifications::widget(); ?>

                    </ul>
                </nav>
            </div>

        </div>
        <!-- /top navigation -->
        <? endif;?>

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
                <?php echo yii\widgets\Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => false,
                        'template' => '<li><a href="'.Yii::$app->urlManager->createUrl('/').'">Admin</a></li>',
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <?=\yiister\gentelella\widgets\FlashAlert::widget()?>
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

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
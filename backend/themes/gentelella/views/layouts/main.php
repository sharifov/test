<?php

/* @var $this \yii\web\View */
/* @var $content string */


use yii\helpers\Html;
use \webvimark\modules\UserManagement\UserManagementModule;

//use common\widgets\Alert;

//use webvimark\modules\UserManagement\UserManagementModule;
//$bundle = yiister\gentelella\assets\Asset::register($this);

//\backend\assets\AppAsset::register($this);
$bundle = \backend\themes\gentelella\assets\Asset::register($this);

//$this->registerCssFile('@backend/themes/gentelella/css/custom.css');
//Yii::$app->view->registerCssFile('@backend/themes/gentelella/css/custom.css', ['depends'=>'yiister\gentelella\assets\Asset']);

?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta name="description" content="Book Air">
    <?
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
    <link rel="stylesheet" href="<?= Yii::$app->getAssetManager()->publish(Yii::getAlias('@backend').'/web/css/style_theme.css')[1];?>"/>
    <?//php $this->head() ?>
    <? /*<link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />*/ ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>

    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="nav-md">
<?php $this->beginBody(); ?>
<div class="container body">

    <div class="main_container">
        <?php if(!Yii::$app->user->isGuest):?>

        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">

                <div class="navbar nav_title" style="border: 0;">
                    <a href="/" class="site_title"><span title="<?=$host?>"><i class="fa fa-dollar"></i> Sales - KIVORK</span></a>
                </div>
                <div class="clearfix"></div>

                <div class="profile">
                    <div class="profile_pic">
                        <?
                            $me = \webvimark\modules\UserManagement\models\User::findOne(Yii::$app->user->id);
                            $default = "identicon";

                            if(!$me || !$me->email) $grav_url = '//www.gravatar.com/avatar/?d=identicon&s=60';
                                else $grav_url = "//www.gravatar.com/avatar/" . md5(strtolower(trim($me->email))) . "?d=identicon&s=128";
                        ?>
                        <?=Html::img($grav_url, ['alt' => 'avatar', 'class' => 'img-circle profile_img'])?>
                    </div>
                    <div class="profile_info">
                        <span>Welcome,</span>
                        <h2><?=Html::encode(Yii::$app->user->identity->username)?></h2>
                    </div>
                </div>
                <!-- /menu prile quick info -->

                <br />

                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

                    <div class="menu_section">
                        <h3>Menu</h3>


                        <?php
                        /*NavBar::begin([
                            'brandLabel' => 'AIE - '.$host,
                            'brandUrl' => Yii::$app->homeUrl,
                            'options' => [
                                'class' => 'navbar-inverse navbar-fixed-top',
                            ],
                        ]);*/




                        //$menuItems[] = ["label" => '<i class="fa fa-home"></i><span>'.Yii::t('menu', 'Home').'</span><small class="label-success label pull-right">new</small>', "url" => "/"];
                        $menuItems[] = ["label" => "Dashboard", "url" => ["/"], "icon" => "bar-chart"];

                        if (Yii::$app->user->isGuest) {
                            $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
                        } else {


                            //$menuItems[] = ["label" => "GDS Info", "url" => ["/site/gdsinfo"], "icon" => "bar-chart"];

                            //if(\webvimark\modules\UserManagement\models\User::hasPermission('userAdmin', $superAdminAllowed = true)) { }

//                            $menuItems[] = [
//                                'label' => 'External AirSearch',
//                                'url' => ['/site/air-search'],
//                                'icon' => 'search',
//                            ];



                            $menuItems[] = [
                                'label' => 'API Logs',
                                'url' => ['/api-log/index'],
                                'icon' => 'sitemap',
                            ];

                            $menuItems[] = [
                                'label' => 'Clients',
                                'url' => ['/client/index'],
                                'icon' => 'user',
                            ];

                            $menuItems[] = [
                                'label' => 'Leads',
                                'url' => ['/leads/index'],
                                'icon' => 'search',
                            ];

                            $menuItems[] = [
                                'label' => 'Flight Segments',
                                'url' => ['/lead-flight-segment/index'],
                                'icon' => 'plane',
                            ];

                            $menuItems[] = [
                                'label' => 'Quotes',
                                'url' => ['/quote/index'],
                                'icon' => 'plane',
                            ];

                            $menuItems[] = [
                                'label' => 'Quote Prices',
                                'url' => ['/quote-price/index'],
                                'icon' => 'dollar',
                            ];



                            $menuItems[] = [
                                'label' => 'Settings',
                                'url' => 'javascript:',
                                'icon' => 'cog',
                                'items' =>  [

                                    ['label' => '<i class="fa fa-product-hunt"></i> Projects', 'url' => ['/settings/projects']],
                                    ['label' => '<i class="fa fa-plane"></i> Airlines', 'url' => ['/settings/airlines']],
                                    ['label' => '<i class="fa fa-plane"></i> Airports', 'url' => ['/settings/airports']],
                                    ['label' => '<i class="fa fa-user-secret"></i> ACL', 'url' => ['/settings/acl']],

                                ]
                            ];


                            /*$menuItems[] = [
                                'label' => 'Airports, Countries, GDS',
                                'url' => 'javascript:',
                                'icon' => 'database',
                                'items' =>  [
                                    ['label' => '<i class="fa fa-database"></i> Airports', 'url' => ['/airport/index']],
                                    ['label' => '<i class="fa fa-database"></i> Countries', 'url' => ['/country/index']],
                                    ['label' => '<i class="fa fa-database"></i> Regions', 'url' => ['/region/index']],
                                    ['label' => '<i class="fa fa-database"></i> GDS List', 'url' => ['/gds/index']],

                                ]
                            ];*/

                            /*$menuItems[] = [
                                'label' => 'Invoices',
                                'url' => ['/invoices/index'],
                                'icon' => 'eur',
                            ];*/


                            /*$menuItems[] = [
                                'label' => 'Booking App',
                                'url' => ['/booking-app/index'],
                                'icon' => 'list',
                            ];*/



                            $menuItems[] = [
                                'label' => Yii::t('menu', 'Languages'),
                                'url' => 'javascript:',
                                'icon' => 'language',
                                'items' =>  [

                                    ['label' => Yii::t('language', 'Language'), 'url' => 'javascript:',
                                        'items' => [
                                            ['label' => Yii::t('language', 'List of languages'), 'url' => ['/translatemanager/language/list']],
                                            ['label' => Yii::t('language', 'Create'), 'url' => ['/translatemanager/language/create']],
                                        ]
                                    ],


                                    ['label' => Yii::t('language', 'Scan'), 'url' => ['/translatemanager/language/scan']],
                                    ['label' => Yii::t('language', 'Optimize'), 'url' => ['/translatemanager/language/optimizer']],
                                    ['label' => Yii::t('language', 'Im-/Export'), 'url' => 'javascript:',
                                        'items' => [
                                            ['label' => Yii::t('language', 'Import'), 'url' => ['/translatemanager/language/import']],
                                            ['label' => Yii::t('language', 'Export'), 'url' => ['/translatemanager/language/export']],
                                        ]
                                    ],
                                ]
                            ];


                            $menuItems[] = [
                                'label' => 'Users',
                                'url' => 'javascript:',
                                'icon' => 'users',
                                'items' => [
                                    ['label' => UserManagementModule::t('back', 'Users'), 'url' => ['/user-management/user/index']],
                                    ['label' => UserManagementModule::t('back', 'Roles'), 'url' => ['/user-management/role/index']],
                                    ['label' => UserManagementModule::t('back', 'Permissions'), 'url' => ['/user-management/permission/index']],
                                    ['label' => UserManagementModule::t('back', 'Permission groups'), 'url' => ['/user-management/auth-item-group/index']],
                                    ['label' => UserManagementModule::t('back', 'Visit log'), 'url' => ['/user-management/user-visit-log/index']],
                                ]
                            ];



                            $menuItems[] = [
                                'label' => 'API Users',
                                'url' => ['/api-user/index'],
                                'icon' => 'users',
                            ];


                            $menuItems[] = [
                                'label' => 'Tools',
                                'url' => 'javascript:',
                                'icon' => 'cog',
                                'items' => [
                                    ['label' => 'Logs', 'url' => ['/log/index']],
                                    ['label' => 'Clear cache', 'url' => ['/tools/clear-cache']],
                                ]
                                //'linkOptions' => ['data-method' => 'post']
                            ];


                            //if(\webvimark\modules\UserManagement\models\User::canRoute('/stats/index', $superAdminAllowed = true)) {
                            //$menuItems[] = ['label' => \backend\widgets\ыуддштSysinfo::widget(['refresh' => 10]), 'url' => ['/stats/index']];
                            //}


                            //$menuItems[] = ['label' => \backend\widgets\Sysinfo::widget(['refresh' => 10]), 'url' => ['/stats/index'], 'icon' => ''];
                        }


                        echo backend\themes\gentelella\widgets\Menu::widget(['items' => $menuItems, 'encodeLabels' => false, 'activateParents' => true]);


                        ?>



                    </div>

                </div>
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
                                    <? /*<a href="login.html"><i class="fa fa-sign-out pull-right"></i> Log Out</a>*/ ?>
                                    <?=Html::a('<i class="fa fa-sign-out pull-right"></i> Log Out', ['/user-management/auth/logout'],
                                        ['title' => "Logout"]) ?>

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



<? /*<div id="custom_notifications" class="custom-notifications dsp_none">
    <ul class="list-unstyled notifications clearfix" data-tabbed_notifications="notif-group">
    </ul>
    <div class="clearfix"></div>
    <div id="notif-group" class="tabbed_notifications"></div>
</div>*/ ?>
<!-- /footer content -->
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
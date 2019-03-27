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

$isAdmin = Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id);

//if($isAdmin) {
    \frontend\assets\NotifyAsset::register($this);
//}

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
<body class="nav-<?= !empty($_COOKIE['menuIsCollapsed']) && $_COOKIE['menuIsCollapsed'] == 'true' ? 'sm' : 'md' ?>">
<?php $this->beginBody(); ?>
<div class="container body">
    <div class="main_container">



        <!-- top navigation -->



                <nav class="" role="navigation">


                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                <?/*<img src="http://placehold.it/128x128" alt="">*/ ?>

                                <?//=Html::img($grav_url, ['alt' => 'avatar'])?>

                                <?/*php
                                    $myRolesModel = \webvimark\modules\UserManagement\models\rbacDB\Role::getUserRoles(Yii::$app->user->id);
                                    $myRoles = [];
                                    if($myRolesModel) {
                                        foreach ($myRolesModel as $role) {
                                            if ($role->name == 'guest') continue;
                                            $myRoles[] = $role->name;
                                        }
                                    }

                                ?>
                                <b><?=implode(', ', $myRoles) ; ?></b>:
                                <?/*=Html::encode(Yii::$app->user->identity->username)*/?>
                                Menu

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
                                    <?=Html::a('<i class="fa fa-home pull-right"></i> Home', ['/site/index'],
                                        ['title' => 'Home']) ?>
                                    <?/*=Html::a('<i class="fa fa-user pull-right"></i> My Profile', ['/site/profile'],
                                        ['title' => "My Profile"])*/ ?>
                                    <?=Html::a('<i class="fa fa-sign-out pull-right"></i> Log Out', ['/site/logout'],
                                        ['title' => "Logout"]) ?>
                                    <?php /*=Html::a('<i class="fa fa-sign-out pull-right"></i> Log Out', ['/user-management/auth/logout'],
                                        ['title' => "Logout"])*/ ?>

                                </li>
                            </ul>
                        </li>


                        <?//= backend\widgets\ChatNotifications::widget(); ?>

                    </ul>
                </nav>



        <!-- /top navigation -->


        <!-- page content -->
        <div class="right_col2" role="main">
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
                        'template' => '<li><a href="'.Yii::$app->urlManager->createUrl('/').'">Home</a></li>',
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


<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
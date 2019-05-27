<?php
/* @var $this \yii\web\View */

use yii\helpers\Html;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;

$js = <<<JS
    $('.alert').fadeOut(3000);
    $('#logout-btn').click(function(e) {
        e.preventDefault();
        $.post($(this).attr('href'), function(data) { });
    });
JS;

$this->registerJs($js);

if (!Yii::$app->user->isGuest) {
    NavBar::begin([
        'options' => [
            'class' => 'navbar navbar-inverse top-navbar',
        ],
        'containerOptions' => [
            'class' => 'top-navbar-collapse',
            'id' => 'top-menu-container'
        ],
        'innerContainerOptions' => [
            'class' => 'container-fluid',
        ]
    ]);
    $menuItems = [];

    \common\components\NavItem::items($menuItems);

    echo Nav::widget([
        'encodeLabels' => false,
        'options' => ['class' => 'nav navbar-nav top-left-menu'],
        'items' => $menuItems,
    ]);
    $menuItems = [];
    $menuItems[] = '<li class="dropdown">'
        . Html::a('<i class="fa fa-user"></i> <span>' . Yii::$app->user->identity->username . ' ('.implode(', ', Yii::$app->user->identity->getRoles()).')</span> <i class="fa fa-angle-down"></i>', '#', [
            'class' => 'dropdown-toggle',
            'data-toggle' => 'dropdown'
        ])
        . '<ul class="dropdown-menu"><li>'
        . Html::a('My Profile', ['site/profile']) . '</li></ul>'
        . '</li>';
    $menuItems[] = '<li>'
        . Html::a('<i class="fa fa-sign-out"></i> <span>Logout</span>', ['site/logout'], [
            'id' => 'logout-btn'
        ])
        . '</li>';

    echo Nav::widget([
        'options' => ['class' => 'nav navbar-nav navbar-right top-right-menu'],
        'items' => $menuItems,
    ]);
    NavBar::end();
}

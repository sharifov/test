<?php

/* @var $this \yii\web\View */

use yii\helpers\Html;

/** @var \common\models\Employee $user */
$user = Yii::$app->user->identity;
?>

<div class="sidebar-footer hidden-small">
    <?php /*<a data-toggle="tooltip" data-placement="top" title="Settings">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>*/ ?>

    <?=Html::a('<span class="glyphicon glyphicon-off" aria-hidden="true"></span>', ['/site/logout'],
        ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Logout']) ?>

    <?php if($user->canRoute('/user-connection/index')):?>
    <?=Html::a('<span class="fa fa-plug"></span>', ['/user-connection/index'],
        ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'User Connections']) ?>
    <?php endif; ?>

    <?php if($user->canRoute('/call/user-map')):?>
    <?=Html::a('<span class="fa fa-map"></span>', ['/call/user-map'],
        ['target' => '_blank', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Call Map']) ?>
    <?php endif; ?>

    <?php if($user->canRoute('/user-connection/stats')):?>
        <?=Html::a('<span class="fa fa-users"></span>', ['/user-connection/stats'],
            ['target' => '_blank', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'User Stats']) ?>
    <?php endif; ?>
</div>
<?php

/* @var $this \yii\web\View */

use yii\helpers\Html;

/** @var \common\models\Employee $user */
$user = Yii::$app->user->identity;
?>


<div class="sidebar-footer hidden-small">
    <div class="col-md-12 form-group" id="search-menu-div" style="display: none">
        <div class="input-group mb-2">
            <div class="input-group-prepend">
                <div class="input-group-text"><i class="fa fa-search"></i></div>
            </div>
            <?=\yii\bootstrap4\Html::input('text', 'search-menu', null, ['class' => 'form-control form-control-sm', 'placeholder' => 'Search menu', 'id' => 'input-search-menu']) ?>
        </div>
    </div>
    <?php /*<a data-toggle="tooltip" data-placement="top" title="Settings">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>*/ ?>

    <?php /*=Html::a('<span class="glyphicon glyphicon-off" aria-hidden="true"></span>', ['/site/logout'],
        ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Logout'])*/ ?>


    <?=Html::a('<span class="fa fa-search"></span>', null,
        ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Search menu', 'id' => 'btn-search-menu-toggle']) ?>

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
<?php
$sideBarMenuUrl = \yii\helpers\Url::to(['/site/side-bar-menu']);

$js = <<<JS
const sideBarMenuUrl = '$sideBarMenuUrl';

$('#btn-search-menu-toggle').on('click', function (e) {
    e.preventDefault();
    $('#search-menu-div').toggle();
});

$('body').on('click', '#btn-remove-search-menu', function (e) {
    e.preventDefault();
    $('#input-search-menu').val('');
    $('#input-search-menu').trigger('keyup');
    return false;
});


$('#input-search-menu').on('keyup', delay(function (e) {
    let val = $(this).val();
    $.pjax.reload({url: sideBarMenuUrl, container: '#pjax-sidebar-menu', push: false, replace: false, 'scrollTo': false, timeout: 3000, async: false, data: {search_text: val}});
}, 400));

/*$('#input-search-menu').on('keyup', delay (e) {
    let val = $(this).val();
    if (val.length > 0) {
        
    }
    $.pjax.reload({container: '#pjax-sidebar-menu', push: false, replace: false, 'scrollTo': false, timeout: 5000, async: false, data: {search_text: val}});
    //e.preventDefault();
    //$('#search-menu-div').toggle();
});*/

function delay(fn, ms) {
  let timer = 0
  return function(...args) {
    clearTimeout(timer)
    timer = setTimeout(fn.bind(this, ...args), ms || 0)
  }
}

$("#pjax-sidebar-menu").on("pjax:start", function() {
    //$('#input-search-menu').prop('disabled', true);
});
$("#pjax-sidebar-menu").on("pjax:end", function() {
    init_sidebar();
    //$('#input-search-menu').prop('disabled', false).attr('disabled', false);
    //alert(123);
});


JS;
$this->registerJs($js, \yii\web\View::POS_READY);

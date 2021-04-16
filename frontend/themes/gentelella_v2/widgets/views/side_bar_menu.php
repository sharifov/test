<?php

/* @var $menuItems array */
/* @var $search_text string */
/* @var $user \common\models\Employee  */

use yii\helpers\Url;
use yii\widgets\Pjax;

?>

    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
<?php Pjax::begin(['id' => 'pjax-sidebar-menu', 'timeout' => 5000, 'enablePushState' => true]); ?>
<?php if ($search_text) :?>
    <!--        <hr style="margin: 5px 5px 5px 5px">-->

    <div class="row">

        <div class="col-md-12">
            <hr style="margin: 3px 5px 3px 5px">
            <div class="row">
                <div class="col-md-9">
                    <div style="margin-left: 15px"><i class="fa fa-search"></i> <i style="color: white">Search by "<?=\yii\helpers\Html::encode($search_text)?>"</i></div>
                </div>
                <div class="col-md-3 text-center">
                    <?=\yii\helpers\Html::a('<i class="fa fa-close"></i>', null, ['id' => 'btn-remove-search-menu'])?>
                </div>
            </div>
            <hr style="margin: 5px 5px 2px 5px">
        </div>

    </div>

<?php endif;?>

    <div class="menu_section">
        <?php
            echo \frontend\themes\gentelella_v2\widgets\Menu::widget([
                'items' => $menuItems,
                'encodeLabels' => false,
                'activateParents' => true,
                'linkTemplate' => '<a href="{url}" {attributes} data-pjax="0">{icon}<span>{label}</span>{badge}</a>'
            ]);
            ?>
    </div>

<?php Pjax::end(); ?>
    </div>

<?php

$js = <<<JS
function updateCounters(url, className, idName) {
    var types = [];
    $("." + className).each(function(i) {
        types.push($(this).data('type'));
    });
    
    $.ajax({
        type: "POST",
        url: url,
        data: {types: types}, 
        dataType: 'json',
        success: function(data){
            if (typeof (data) != "undefined" && data != null) {
                $.each( data, function( key, val ) {
                    if (val != 0) {
                        $("#" + idName + "-" + key).html(val);
                    } else if (val == 0) {
                        $("#" + idName + "-" + key).html('');
                    }
                });
            }
        },
        error: function(data){
            console.log(data);
        }, 
    });    
    
}
JS;
$this->registerJs($js, $this::POS_LOAD);

if (Yii::$app->user->can('leadSection')) {
    $urlBadgesCount = Url::to(['/badges/get-badges-count']);
    $this->registerJs("updateCounters('$urlBadgesCount', 'bginfo', 'badges');", $this::POS_LOAD);
}
if (Yii::$app->user->can('caseSection')) {
    $urlCasesQCount = Url::to(['/cases-q-counters/get-q-count']);
    $this->registerJs("updateCounters('$urlCasesQCount', 'cases-q-info', 'cases-q');", $this::POS_LOAD);
}

    $urlOrderCount = Url::to(['/order/order-q/get-badges-count']);
    $this->registerJs("updateCounters('$urlOrderCount', 'order-q-info', 'order-q');", $this::POS_LOAD);

if (Yii::$app->user->can('/qa-task/qa-task-queue/count')) {
    $urlQaTaskCount = Url::to(['/qa-task/qa-task-queue/count']);
    $this->registerJs("updateCounters('$urlQaTaskCount', 'qa-task-info', 'qa-task-q');", $this::POS_LOAD);
}
if ($user->canCall()) {
    $urlVoiceMailRecordCount = Url::to(['/voice-mail-record/count']);
    $this->registerJs("
    function updateVoiceRecordCounters() {
        updateCounters('$urlVoiceMailRecordCount', 'voice-mail-record', 'voice-mail-record');
    }
    window.updateVoiceRecordCounters = updateVoiceRecordCounters;
    window.updateVoiceRecordCounters();
    ", $this::POS_LOAD);
}

$js = <<<JS
$('.nav.side-menu [data-ajax-link]').on('click', function (e) {
    e.preventDefault();
    let ajaxLink = $(this).data('ajax-link');
    let modalTitle = $(this).data('modal-title');

    if (ajaxLink) {
        let url = $(this).attr('href');

        var modal = $('#modal-md');
        $.ajax({
            type: 'post',
            url: url,
            data: {},
            dataType: 'html',
            beforeSend: function () {
                modal.find('.modal-body').html('<div style="text-align:center;font-size: 40px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
                modal.find('.modal-title').html(modalTitle);
                modal.modal('show');
            },
            success: function (data) {
                modal.find('.modal-body').html(data);
                modal.find('.modal-title').html(modalTitle);
                $('#preloader').addClass('d-none');
            },
            error: function () {
                new PNotify({
                    title: 'Error',
                    type: 'error',
                    text: 'Internal Server Error. Try again letter.',
                    hide: true
                });
                setTimeout(function () {
                    $('#modal-md').modal('hide');
                }, 300)
            },
        })
    }
});
JS;
$this->registerJs($js);

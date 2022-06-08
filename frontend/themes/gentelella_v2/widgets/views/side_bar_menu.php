<?php

/* @var $menuItems array */
/* @var $search_text string */
/* @var $user \common\models\Employee  */

use modules\featureFlag\FFlag;
use src\services\badges\BadgesDictionary;
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
/** @fflag FFlag::FF_KEY_BADGE_COUNT_ENABLE, Badge Count Enable/Disable */
if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BADGE_COUNT_ENABLE)) :
    $js = <<<JS
        let badgesCollection = [];
JS;
    $this->registerJs($js, $this::POS_LOAD);

    if (Yii::$app->user->can('leadSection')) {
        $this->registerJs("let leadTypes = [];
            $('.bginfo').each(function(i) {
                leadTypes.push($(this).data('type'));
            });
            badgesCollection.push({objectKey:'" . BadgesDictionary::KEY_OBJECT_LEAD . "', idName:'badges', types: leadTypes});", $this::POS_LOAD);
    }
    if (Yii::$app->user->can('caseSection')) {
        $this->registerJs("
            let casesTypes = [];
            $('.cases-q-info').each(function(i) {
                casesTypes.push($(this).data('type'));
            });
            badgesCollection.push({objectKey:'" . BadgesDictionary::KEY_OBJECT_CASES . "', idName:'cases-q', types: casesTypes});", $this::POS_LOAD);
    }
    if (Yii::$app->user->can('/order/order-q/get-badges-count')) {
        $this->registerJs("
            let orderTypes = [];
            $('.order-q-info').each(function(i) {
                orderTypes.push($(this).data('type'));
            });
            badgesCollection.push({objectKey:'" . BadgesDictionary::KEY_OBJECT_ORDER . "', idName:'order-q', types: orderTypes});", $this::POS_LOAD);
    }
    if (Yii::$app->user->can('/qa-task/qa-task-queue/count')) {
        $this->registerJs("
            let qaTaskTypes = [];
            $('.qa-task-info').each(function(i) {
                qaTaskTypes.push($(this).data('type'));
            });
            badgesCollection.push({objectKey:'" . BadgesDictionary::KEY_OBJECT_QA_TASK . "', idName:'qa-task-q', types: qaTaskTypes});", $this::POS_LOAD);
    }
    if ($user->canCall()) {
        $this->registerJs("
            let voiceMailTypes = [];
            $('.voice-mail-record').each(function(i) {
                voiceMailTypes.push($(this).data('type'));
            });
            badgesCollection.push({objectKey:'" . BadgesDictionary::KEY_OBJECT_VOICE_MAIL . "', idName:'voice-mail-record', types:voiceMailTypes});", $this::POS_LOAD);
    }

    $urlBadgesCount = Url::to(['/badges/badges-count']);
    $js = <<<JS
        if (badgesCollection.length) {
            setTimeout(function() {
                $.ajax({
                    url: "{$urlBadgesCount}",
                    type: 'POST',
                    data: {badgesCollection: badgesCollection},
                    dataType: 'json'
                })
                .done(function(dataResponse) {
                    if (dataResponse.status === 1) {
                        $.each(dataResponse.data, function(idName, badgets) {
                            $.each(badgets, function(key, val) {
                                if (val !== 0) {
                                    $("#" + idName + "-" + key).html(val);
                                } else if (val === 0) {
                                    $("#" + idName + "-" + key).html('');
                                }
                            });
                        });
                    } else if (dataResponse.message.length) {
                        console.error('Badges Count Error. Message: ' + Response.message);
                    } else {
                        console.error('Badges Count Error.');
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log({
                        jqXHR : jqXHR,
                        textStatus : textStatus,
                        errorThrown : errorThrown
                    });
                })
                .always(function(jqXHR, textStatus, errorThrown) {});
            }, 200);
        }
JS;
    $this->registerJs($js, $this::POS_LOAD);
endif ;

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
                createNotifyByObject({
                    title: 'Error',
                    type: 'error',
                    text: 'Internal Server Error. Try again letter.',
                    hide: true
                });
                setTimeout(function () {
                    $('#modal-md').modal('hide');
                }, 300)
            }
        })
    }
});
JS;
$this->registerJs($js);

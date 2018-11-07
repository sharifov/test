<?php
/**
 * @var $leadForm LeadForm
 * @var $quotesProvider ActiveProvider
 */

use yii\bootstrap\Html;
use frontend\models\LeadForm;
use yii\bootstrap\Modal;
use yii\widgets\ListView;
use common\models\Quote;

//$bundle = \frontend\themes\gentelella\assets\LeadAsset::register($this);
//$this->registerCssFile('/css/style-req.css');
$userId = Yii::$app->user->id;

$is_manager = false;
if(Yii::$app->authManager->getAssignment('admin', $userId) || Yii::$app->authManager->getAssignment('supervision', $userId)) {
    $is_manager = true;
}

if (!$leadForm->getLead()->isNewRecord) {
    $flowTransitionUrl = \yii\helpers\Url::to([
        'lead/flow-transition',
        'leadId' => $leadForm->getLead()->id
    ]);

    $checkUpdatesUrl = \yii\helpers\Url::to([
        'lead/check-updates',
        'leadId' => $leadForm->getLead()->id,
        'lastUpdate' => date('Y-m-d H:i:s')
    ]);

    $js = <<<JS
    function checkRequestUpdates(checkUrl) {
        $.get(checkUrl)
            .done(function (data) {
                if (data.logs.length != 0) {
                    $('#agents-activity-logs').html(data.logs);
                }
                if (data.needRefresh) {
                    var modal = $('#modal-error');
                    modal.find('.modal-body').html(data.content);
                    modal.modal({
                        backdrop: 'static',
                        show: true
                    });
                } else {
                    setTimeout(function() {
                        checkRequestUpdates(data.checkUpdatesUrl);
                    }, 120000);
                }
            })
            .fail(function () {
                setTimeout(function() {
                    checkRequestUpdates('$checkUpdatesUrl');
                }, 120000);
            });
    }
    setTimeout(function() {
        checkRequestUpdates('$checkUpdatesUrl');
    }, 120000);

    $('#view-flow-transition').click(function() {
        $('#preloader').removeClass('hidden');
        var editBlock = $('#get-request-flow-transition');
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load('$flowTransitionUrl', function( response, status, xhr ) {
            $('#preloader').addClass('hidden');
            editBlock.modal('show');
        });
    });
JS;

    $this->registerJs($js);
}
?>
<?php  $js = <<<JS
$(document).on('click','.quote_details__btn', function (e) {
    e.preventDefault();
    var modal = $('#flight-details__modal');
    modal.find('.modal-header h2').html($(this).data('title'));
    var target = $($(this).data('target')).html();
    modal.find('.modal-body').html(target);
    modal.modal('show');
});
JS;

$this->registerJs($js);
?>
<?php $extraPriceUrl = \yii\helpers\Url::to(['quote/extra-price']);
$declineUrl = \yii\helpers\Url::to(['quote/decline']);
$statusLogUrl = \yii\helpers\Url::to(['quote/status-log']);
$previewEmailUrl = \yii\helpers\Url::to(['quote/preview-send-quotes']);
$leadId = $leadForm->getLead()->id;

if ($leadForm->mode != $leadForm::VIEW_MODE) {
    $js = <<<JS

    $('[data-toggle="tooltip"]').tooltip();
    $(document).on('click', '.send-quotes-to-email', function () {
        var urlModel = $(this).data('url');
        var email = $('#send-to-email').val();
        var quotes = Array();
        $('.quotes-uid:checked').each(function(idx, elm){
            quotes.push($(elm).val());
        });
        if (quotes.length == 0) {
            return null;
        }
        $('#btn-send-quotes').popover('hide');
        $('#preloader').removeClass('hidden');
        var dataPost = {leadId: $leadId, email:email, quotes: quotes };
        $.ajax({
            url: urlModel,
            type: 'post',
            data: dataPost,
            success: function (data) {
                var editBlock = $('#preview-send-quotes');
                editBlock.find('.modal-body').html(data);
                editBlock.modal('show');

                $('#preloader').addClass('hidden');
            },
            error: function (error) {
                $('#preloader').addClass('hidden');
                console.log('Error: ' + error);
            }
        });
    });

    $('#btn-send-quotes').popover({
        html: true,
        placement: 'top',
        content: function () {
            $('#send-to-email').html('');
            $('.email').each(function(idx, elm){
                var val = $(elm).val();
                if(val != ''){
                    $('#send-to-email').append('<option value="'+val+'">'+val+'</option>');
                }
            });
            return $(".js-pop-emails-content").html();
        }
    });
    $('#lg-btn-send-quotes').click(function() {
        $('#btn-send-quotes').trigger('click');
    });

    $('.ext-mark-up').keyup(function (event) {
        var key = event.keyCode ? event.keyCode : event.which;
        validatePriceField($(this), key);
    });
    $('.ext-mark-up').change(function (event) {
        if ($(this).val().length == 0) {
            $(this).val(0);
        }
        var element = $(this);
        $.ajax({
            type: 'post',
            url: '$extraPriceUrl',
            data: {'quote_uid': $(this).data('quote-uid'), 'value': $(this).val(), 'pax_type': $(this).data('pax-type')},
            success: function (data) {
                if (!jQuery.isEmptyObject(data)) {
                    var sell = element.parent().parent().find('.sellingPrice-'+data.uid),
                            totalSell = $('#'+data.uid).find('.total-sellingPrice-'+data.uid),
                            totalMarkup = $('#'+data.uid).find('.total-markup-'+data.uid);

                        sell.text(data.actual.sellingPrice);
                        totalSell.text(data.total.sellingPrice);
                        totalMarkup.text(data.total.markup);

                        $('#isChangedMarkup-'+data.uid).removeClass('hidden');
                    }
            },
            error: function (error) {
            console.log('Error: ' + error);
            }
        });
    });

    $('.view-status-log').click(function(e){
        e.preventDefault();
        $('#preloader').removeClass('hidden');
        var editBlock = $('#get-quote-status-log');
        editBlock.find('.modal-body').html('');
        var id = $(this).attr('data-id');
        editBlock.find('.modal-body').load('$statusLogUrl?quoteId='+id, function( response, status, xhr ) {
            $('#preloader').addClass('hidden');
            editBlock.modal('show');
        });
    });

    $('#btn-declined-quotes').click(function() {
        var quotes = Array();
        $('.quotes-uid:checked').each(function(idx, elm){
            quotes.push($(elm).val());
        });
        if (quotes.length == 0) {
            return null;
        }
        var dataPost = {quotes: quotes};
        $('#preloader').removeClass('hidden');
        $.ajax({
            type: 'post',
            url: '$declineUrl',
            data: dataPost,
            success: function (data) {
                $('#preloader').addClass('hidden');
                if (data.errors.length != 0) {
                    $('#sent-messages').removeClass('hidden').addClass('alert-danger').removeClass('alert-success');
                    $('#sent-messages').find('.fa-exclamation-triangle').addClass('hidden');
                    $('#sent-messages').find('.fa-times-circle').removeClass('hidden');
                    var errors = Array();
                    $.each(data.errors, function(i, elm){errors.push(elm)});
                    $('#sent-messages').find('div').html(errors.join('<br>'));
                    $('#sent-messages').show();
                    $('#sent-messages').fadeOut(10000);
                } else {
                    $('#sent-messages').removeClass('hidden').addClass('alert-success').removeClass('alert-danger');
                    $('#sent-messages').find('.fa-exclamation-triangle').removeClass('hidden');
                    $('#sent-messages').find('.fa-times-circle').addClass('hidden');
                    $('#sent-messages').find('div').html(quotes.length+' quotes was successfully Declined');
                    $('#sent-messages').show();
                    $('#sent-messages').fadeOut(10000);

                    $.each(quotes, function(idx, quote) {
                        $('#q-status-'+quote).text('Declined');
                        $('#q-status-'+quote).attr('class', 'sl-quote__status status-label label label-danger');
                    });

                    $.each($('.quotes-uid:checked'), function(idx, elm) {
                        elm.parentElement.classList.add('hidden');
                    });
                }
            },
            error: function (error) {
                $('#preloader').addClass('hidden');
                console.log('Error: ' + error);
            }
        });
    });
JS;
    $this->registerJs($js);
}
?>
<div class="page-header">
    <div class="container-fluid">
        <div class="page-header__wrapper">
            <h2 class="page-header__title">
            <?= Html::encode($this->title) ?>
            <?php
            $lead = $leadForm->getLead();
            if(!empty($lead->clone_id)){
                printf(" <a title=\"%s\" href=\"%s\">(Cloned from %s)</a> ",
                    "Clone reason: ".$lead->description,
                    \yii\helpers\Url::to([
                    'lead/quote',
                    'type' => 'processing',
                    'id' => $lead->clone_id
                ]),$lead->clone_id);
            }
            ?>
            <?php if ($leadForm->getLead()->isNewRecord) : ?>
            	<span class="label status-label label-info">New</span>
            <?php else:?>
            	<?= $leadForm->getLead()->getStatusLabel() ?>
            <?php endif;?>
            </h2>
            <div class="page-header__general">
                <?php if (!$leadForm->getLead()->isNewRecord) : ?>
                    <?php if (!empty($leadForm->getLead()->employee_id)) : ?>
                        <div class="page-header__general-item">
                            <strong>Assigned to:</strong>
                            <i class="fa fa-user"></i> <?= $leadForm->getLead()->employee->username ?>
                        </div>
                    <?php endif; ?>
                    <div class="page-header__general-item">
                        <strong>Client <i class="fa fa-clock-o"></i>:</strong>
                        <?= $leadForm->getLead()->getClientTime(); ?>
                    </div>
                    <div class="page-header__general-item">
                        <strong>UID:</strong>
                        <span><?= Html::a(sprintf('%08d', $leadForm->getLead()->uid), '#', ['id' => 'view-flow-transition']) ?></span>
                    </div>

                    <div class="page-header__general-item">
                        <strong>Market:</strong>
                        <span><?= $leadForm->getLead()->project->name.' - '.$leadForm->getLead()->source->name?></span>
                    </div>
                    <div class="page-header__general-item">
                        <?= $this->render('partial/_rating', [
                            'lead' => $leadForm->getLead()
                        ]) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="main-sidebars">
    <div class="panel panel-main">
        <?= $this->render('partial/_actions', [
            'leadForm' => $leadForm
        ]);
        ?>

        <div class="sl-request-content">
            <?= \common\widgets\Alert::widget() ?>

            <?php if (!$leadForm->getLead()->isNewRecord) : ?>

                <div class="row">
                    <div class="col-md-12">
                        <?php if(!$leadForm->getLead()->l_answered): ?>

                            <?php if($leadForm->getLead()->status == \common\models\Lead::STATUS_PROCESSING):?>
                                <?= Html::a(($leadForm->getLead()->l_answered ? '<i class="fa fa-commenting-o"></i>Make UnAnswered' : '<i class="fa fa-commenting"></i> Make Answered'), ['lead/update2', 'id' => $leadForm->getLead()->id, 'act' => 'answer'], [
                                    'class' => 'btn '.($leadForm->getLead()->l_answered ? 'btn-success' : 'btn-info'),
                                    'data-pjax' => false,
                                    'data' => [
                                        'confirm' => 'Are you sure?',
                                        'method' => 'post',
                                        'pjax' => 0
                                    ],
                                ]) ?>
                            <? else: ?>
                                <span class="badge badge-warning"><i class="fa fa-commenting-o"></i> ANSWERED: false</span>
                            <? endif;?>

                        <? else: ?>
                            <span class="badge badge-success"><i class="fa fa-commenting-o"></i> ANSWERED: true</span>
                        <? endif; ?>

                        <?php if($is_manager): ?>
                            <span class="badge badge-info" title="Grade"><i class="fa fa-retweet"></i> GRADE: <?=$leadForm->getLead()->l_grade?></span>
                        <? endif; ?>
                    </div>

                </div>
                <br>


                <?= $this->render('partial/_task_list', [
                    'lead' => $leadForm->getLead()
                ]); ?>

            <?php endif; ?>

            <?= $this->render('partial/_flightDetails', [
                'leadForm' => $leadForm
            ]);
            ?>

            <?php \yii\widgets\Pjax::begin(); ?>
			<?= ListView::widget([
			    'dataProvider' => $quotesProvider,
			    'itemView' => 'partial/_quote_item',
                'viewParams' => [
                    'appliedQuote' => $lead->getAppliedAlternativeQuotes(),
                    'leadId' => $lead->id,
                    'leadForm' => $leadForm
                ],
			]);?>
            <?php \yii\widgets\Pjax::end() ?>

            <?php if (!$leadForm->getLead()->isNewRecord && count($leadForm->getLead()->getQuotes())) {

                echo $this->render('partial/_quotes', [
                    'quotes' => array_reverse($leadForm->getLead()->getQuotes()),
                    'lead' => $leadForm->getLead(),
                    'leadForm' => $leadForm
                ]);
            } ?>


            <?php if (!$leadForm->getLead()->isNewRecord) : ?>

                <?/*= $this->render('partial/_task_list', [
                    'lead' => $leadForm->getLead()
                ]);*/ ?>

                <?= $this->render('partial/_notes', [
                    'notes' => $leadForm->getLead()->getNotes()
                ]); ?>
                <div class="panel panel-neutral panel-wrapper history-block">
                    <div class="panel-heading collapsing-heading">
                        <a data-toggle="collapse" href="#agents-activity-logs" aria-expanded="false"
                           class="collapsing-heading__collapse-link collapsed">
                            Activity Logs
                            <i class="collapsing-heading__arrow"></i>
                        </a>
                    </div>
                    <div class="collapse" id="agents-activity-logs" aria-expanded="false" style="">
                        <?= $this->render('partial/_leadLog', [
                            'logs' => $leadForm->getLead()->getLogs()
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <aside class="sidebar right-sidebar sl-right-sidebar">
    	 <?php if($leadForm->getLead()->status == \common\models\Lead::STATUS_FOLLOW_UP && $leadForm->getLead()->employee_id != Yii::$app->user->id && $is_manager):?>

            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Warning!</h4>
                <p>Client information is not available for this status (FOLLOW UP)!</p>
            </div>

        <? else: ?>
            <?= $this->render('partial/_client', [
                'leadForm' => $leadForm
            ]);
            ?>
        <? endif; ?>
        <?= $this->render('partial/_preferences', [
            'leadForm' => $leadForm
        ]);
        ?>
    </aside>
</div>
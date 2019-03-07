<?php
/**
 * @var $leadForm LeadForm
 * @var $comForm \frontend\models\CommunicationForm
 * @var $previewEmailForm \frontend\models\LeadPreviewEmailForm
 * @var $previewSmsForm \frontend\models\LeadPreviewSmsForm
 * @var $quotesProvider \yii\data\ActiveDataProvider
 * @var $dataProviderCommunication \yii\data\ActiveDataProvider
 */

use yii\bootstrap\Html;
use frontend\models\LeadForm;
use yii\bootstrap\Modal;
use yii\widgets\ListView;
use common\models\Quote;


$bundle = \frontend\themes\gentelella\assets\AssetLeadCommunication::register($this);

//$bundle = \frontend\themes\gentelella\assets\LeadAsset::register($this);
//$this->registerCssFile('/css/style-req.css');
$userId = Yii::$app->user->id;

$is_manager = false;
$is_admin = (Yii::$app->authManager->getAssignment('admin', $userId));
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

$(document).on('change', '.quote__heading input:checkbox', function () {
    if ($(this).is(":checked")) {
        $(this).parents('.quote').addClass("quote--selected");
    } else {
        $(this).parents('.quote').removeClass("quote--selected");
    }
});
JS;

$this->registerJs($js);
?>

<div class="page-header">
    <div class="container-fluid">
        <div class="page-header__wrapper">
            <h2 class="page-header__title">
            <?= Html::encode($this->title) ?>
            <?php
            $lead = $leadForm->getLead();
            if(!empty($lead->clone_id)){

                $cloneLead = \common\models\Lead::findOne($lead->clone_id);

                printf(" <a title=\"%s\" href=\"%s\">(Cloned from %s)</a> ",
                    "Clone reason: ".$lead->description,
                    \yii\helpers\Url::to([
                    'lead/view',
                    'uid' => $cloneLead->uid
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
                        <strong>Client:</strong>
                        <?= $leadForm->getLead()->getClientTime2(); ?>
                    </div>
                    <div class="page-header__general-item">
                        <strong>UID:</strong>
                        <span><?= Html::a($leadForm->getLead()->uid, '#', ['id' => 'view-flow-transition']) ?></span>
                    </div>

                    <div class="page-header__general-item">
                        <strong>Market:</strong>
                        <span><?= (($leadForm->getLead()->project)?$leadForm->getLead()->project->name:'').
                        (($leadForm->getLead()->source)?' - '.$leadForm->getLead()->source->name:'')?></span>
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

			<?php if (!$leadForm->getLead()->isNewRecord):?>
			<?php
    			$extraPriceUrl = \yii\helpers\Url::to(['quote/extra-price']);
                $declineUrl = \yii\helpers\Url::to(['quote/decline']);
                $statusLogUrl = \yii\helpers\Url::to(['quote/status-log']);
                $previewEmailUrl = \yii\helpers\Url::to(['quote/preview-send-quotes']);
                $leadId = $leadForm->getLead()->id;?>
                <?php
if ($leadForm->mode != $leadForm::VIEW_MODE) {
    $js = <<<JS

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

    $(document).on('click', '.send-quotes-to-email-new', function () {
        var urlModel = $(this).data('url');
        var email = $('#send-to-email-new').val();
        var lang = $('#send-to-email-lng').val();
        var quotes = Array();
        $('.quotes-uid:checked').each(function(idx, elm){
            quotes.push($(elm).val());
        });
        if (quotes.length == 0) {
            return null;
        }
        $('#preloader').removeClass('hidden');
        var dataPost = {leadId: $leadId, email:email, lang: lang,  quotes: quotes };
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
        sanitize: false,
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
    $('#send-quotes-btn-popover').click(function(){
        $('.email').each(function(idx, elm){
            var val = $(elm).val();
            if(val != ''){
                $('#send-to-email-new').append('<option value="'+val+'">'+val+'</option>');
            }
        });
    });
    $('#lg-btn-send-quotes').click(function() {
        $('#btn-send-quotes').trigger('click');
    });

    $(document).on('keyup','.ext-mark-up', function (event) {
        var key = event.keyCode ? event.keyCode : event.which;
        validatePriceField($(this), key);
    });
    $(document).on('change','.ext-mark-up', function (event) {
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
                            totalSell = $('.total-sellingPrice-'+data.uid),
                            totalMarkup = $('.total-markup-'+data.uid);

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

    $(document).on('click','.view-status-log' ,function(e){
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
                $.pjax.reload({container: '#quotes_list', async: false});
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
                <?php if ($leadForm->mode != $leadForm::VIEW_MODE) : ?>
                <div class="btn-wrapper pt-20 mb-20">
                    <?= Html::button('<i class="fa fa-eye-slash"></i> Declined Quotes', [
                        'class' => 'btn btn-primary btn-sm',
                        'id' => 'btn-declined-quotes',
                    ]) ?>
                    <!--Button Send-->
                    <span class="btn-group">
                        <?= Html::button('<i class="fa fa-send"></i> Send Email Quotes', [
                            'class' => 'btn btn-sm btn-success',
                            'id' => 'lg-btn-send-quotes',
                        ]) ?>
                        <?= Html::button('<span class="caret"></span>', [
                            'id' => 'btn-send-quotes',
                            'class' => 'btn btn-sm btn-success dropdown-toggle sl-popover-btn',
                            'data-toggle' => 'popover',
                            'title' => '',
                            'data-original-title' => 'Select Emails',
                        ]) ?>
                    </span>
                    <div class="hidden js-pop-emails-content sl-popover-emails">
                        <label for="send-to-email" class="select-wrap-label mb-20" style="width:250px;">
                            <?= Html::dropDownList('send_to_email', null, [], [
                                'class' => 'form-control',
                                'id' => 'send-to-email'
                            ]) ?>
                        </label>
                        <div>
                            <?= Html::button('Send', [
                                'class' => 'btn btn-sm btn-success send-quotes-to-email',
                                'id' => 'btn-send-quotes-email',
                                'data-url' => \yii\helpers\Url::to(['quote/preview-send-quotes'])
                            ]) ?>
                        </div>
                    </div>

                    <?php if($is_admin):?>
                    <!-- New button send -->
                    <?= Html::button('<i class="fa fa-envelope"></i> Send Email Quotes 2', [
                        'class' => 'btn btn-primary popover-class',
                        'title' => 'Select Emails',
                        'data-toggle' => 'popover',
                        'data-html' => 'true',
                        'data-title' => 'Select Emails',
                        'data-trigger' => 'click',
                        'id' => 'send-quotes-btn-popover',
                        'data-placement' => 'top',
                        'data-container' => 'body',
                            'data-sanitize' => 'false',
                        'data-content' => '<label for="send-to-email-new" class="select-wrap-label mb-20">'.
                                                Html::dropDownList('send_to_email', null, [], [
                                                    'class' => 'form-control',
                                                    'id' => 'send-to-email-new'
                                                ]).'
                                            </label>
                                            <label for="send-to-email-lng" class="select-wrap-label mb-20">'.
                                                Html::dropDownList('send_to_email_lng', null,
                                                    \lajax\translatemanager\models\Language::getLanguageNames(true), [
                                                    'class' => 'form-control',
                                                    'id' => 'send-to-email-lng'
                                                ]).'
                                            </label>
                                            <div>'.
                                                Html::button('Send', [
                                                    'class' => 'btn btn-success send-quotes-to-email-new',
                                                    'id' => 'btn-send-quotes-email-new',
                                                    'data-url' => \yii\helpers\Url::to(['quote/preview-send-quotes-new'])
                                                ]).'</div>',
                    ]);?>
                    <?php endif;?>
                </div>
                <div id="sent-messages" class="alert hidden">
                    <i class="fa fa-exclamation-triangle hidden"></i>
                    <i class="fa fa-times-circle hidden"></i>
                    <div></div>
                </div>
            <?php endif; ?>

            <?php \yii\widgets\Pjax::begin(['id' => 'quotes_list']); ?>
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
            <?php endif;?>


            <?php if (!$leadForm->getLead()->isNewRecord) : ?>
                <?php if (!$is_admin && $leadForm->mode === $leadForm::VIEW_MODE) : ?>
                    <div class="alert alert-warning" role="alert">You do not have access to view Communication messages.</div>
                <?php else: ?>
                    <?= $this->render('communication/lead_communication', [
                            'leadForm'      => $leadForm,
                            'previewEmailForm' => $previewEmailForm,
                            'previewSmsForm' => $previewSmsForm,
                            'comForm'       => $comForm,
                            'leadId'        => $lead->id,
                            'dataProvider'  => $dataProviderCommunication,
                            'isAdmin'       => $is_admin
                        ]);
                    ?>
                <?php endif;?>
            <?php endif;?>


            <?php if (!$leadForm->getLead()->isNewRecord) : ?>

                <?/*= $this->render('partial/_task_list', [
                    'lead' => $leadForm->getLead()
                ]);*/ ?>

                <?= $this->render('partial/_notes', [
                    'notes' => $leadForm->getLead()->notes
                ]); ?>
                <div class="panel panel-success panel-wrapper history-block">
                    <div class="panel-heading collapsing-heading">
                        <a data-toggle="collapse" href="#agents-activity-logs" aria-expanded="false"
                           class="collapsing-heading__collapse-link collapsed">
                            Activity Logs
                            <i class="collapsing-heading__arrow"></i>
                        </a>
                    </div>
                    <div class="collapse" id="agents-activity-logs" aria-expanded="false" style="">
                        <?= $this->render('partial/_leadLog', [
                            'logs' => $leadForm->getLead()->leadLogs
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

	<aside class="sidebar right-sidebar sl-right-sidebar">
    	 <?php if($leadForm->mode == $leadForm::VIEW_MODE && (!Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) && !Yii::$app->authManager->getAssignment('supervision', Yii::$app->user->id))):?>
			<div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Warning!</h4>
                <p>Client information is not available in VIEW MODE, please take lead!</p>
            </div>

    	 <?php elseif(!$is_manager && ( $leadForm->getLead()->status == \common\models\Lead::STATUS_FOLLOW_UP || ($leadForm->getLead()->status == \common\models\Lead::STATUS_PENDING && !$leadForm->getLead()->isNewRecord) ) && $leadForm->getLead()->employee_id != Yii::$app->user->id):?>

            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Warning!</h4>
                <p>Client information is not available for this status (<?=strtoupper($leadForm->getLead()->getStatusName())?>)!</p>
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


<style>
    #scrollUp{bottom:20px;right:20px;background:#555;color:#fff;font-size:12px;font-family:sans-serif;text-decoration:none;opacity:.9;padding:10px 20px;-webkit-border-radius:16px;-moz-border-radius:16px;border-radius:16px;-webkit-transition:background 200ms linear;-moz-transition:background 200ms linear;transition:background 200ms linear}#scrollUp:hover{background:#000}
</style>

<?php

$js = <<<JS
$(function () {
    $.scrollUp({
        scrollName: 'scrollUp', // Element ID
        topDistance: '300', // Distance from top before showing element (px)
        topSpeed: 300, // Speed back to top (ms)
        animation: 'fade', // Fade, slide, none
        animationInSpeed: 200, // Animation in speed (ms)
        animationOutSpeed: 200, // Animation out speed (ms)
        scrollText: 'Scroll to top', // Text for element
        activeOverlay: true, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
    });
  
    //$("[data-toggle='tooltip']").tooltip(); 
    //$("[data-toggle='popover']").popover({sanitize: false}); 
  
});
JS;

$this->registerJs($js);
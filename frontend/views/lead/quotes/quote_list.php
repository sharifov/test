<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $leadForm \frontend\models\LeadForm
 * @var $is_manager boolean
 */

use common\models\LeadCallExpert;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;

?>
<style>
.select2-container--krajee {
    display: block;
    z-index: 9999;
}
</style>

<?php Pjax::begin(['id' => 'quotes_list', 'timeout' => 10000]); ?>
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-folder-o"></i> Quotes</h2>
        <ul class="nav navbar-right panel_toolbox">
            <?php if ($leadForm->mode !== $leadForm::VIEW_MODE || $is_manager) : ?>
            <li>
                <?= $this->render('_quote_clone_by_id', ['lead' => $leadForm->getLead()])?>
            </li>
            <li>
                <?=Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>
            </li>
            <li>
                <?php if($lead->leadFlightSegmentsCount):?>
                    <?=Html::a('<i class="fa fa-search warning"></i> Quick Search', null, ['class' => '', 'id' => 'quick-search-quotes-btn', 'data-url' => Url::to(['quote/get-online-quotes', 'leadId' => $leadForm->getLead()->id])])?>
                <?php else: ?>
                    <span class="badge badge-warning"><i class="fa fa-warning"></i> Warning: Flight Segments is empty!</span>
                <?php endif; ?>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li> <?= Html::a('<i class="fa fa-remove"></i> Decline Quotes', null, [
                            //'class' => 'btn btn-primary btn-sm',
                            'id' => 'btn-declined-quotes',
                        ]) ?>
                    </li>
                </ul>
            </li>
            <?php endif; ?>

            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>


        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_quote_item',
            'emptyText' => '<div class="text-center">Not found quotes</div><br>',
            //'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
            'viewParams' => [
                'appliedQuote' => $lead->getAppliedAlternativeQuotes(),
                'leadId' => $lead->id,
                'leadForm' => $leadForm,
                'isManager' => $is_manager,
            ],
            'itemOptions' => [
                //'class' => 'item',
                'tag' => false,
            ],
        ]);?>

    </div>
</div>
<?php Pjax::end() ?>


<?php
$extraPriceUrl = \yii\helpers\Url::to(['quote/extra-price']);
$declineUrl = \yii\helpers\Url::to(['quote/decline']);
$statusLogUrl = \yii\helpers\Url::to(['quote/status-log']);
$previewEmailUrl = \yii\helpers\Url::to(['quote/preview-send-quotes']);
$leadId = $leadForm->getLead()->id;?>

<?php

// Menu details

$js = <<<JS
$(document).on('click','.btn-quote-details', function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var modal = $('#modal-info-d');
        modal.find('.modal-header h2').text($(this).data('title'));
        modal.find('.modal-body').html('');
        $('#preloader').removeClass('hidden');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            $('#preloader').addClass('hidden');
            modal.modal('show');
        });
    });
JS;
$this->registerJs($js);

// Menu status log

$js = <<<JS
 $(document).on('click', '.view-status-log', function(e){
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
JS;
$this->registerJs($js);

//Menu esc for Reservation dump

$js = <<<JS
 $(document).on('keyup', function (event) {
        if (event.which === 27) {
            $('.popover-class').popover('hide');
        }
    });
JS;
$this->registerJs($js);

// Menu clone
if ($leadForm->getLead()->isProcessing()) {
    if ($is_manager || $leadForm->getLead()->isOwner(Yii::$app->user->id)) {
        $js = <<<JS
 $(document).on('click','.add-clone-alt-quote', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let uid = $(this).data('uid');
        let modal = $('#modal-lg');
        if (uid != 0) {
            $('#modal-lg-label').html('Clone quote #' + uid);
        } else {
            $('#modal-lg-label').html('Add quote');
        }
        modal.find('.modal-body').html('');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            $('#cancel-alt-quote').attr('data-type', 'direct');
            modal.modal({
              // backdrop: 'static',
              show: true
            });
        });
    });
JS;
        $this->registerJs($js);
    }
}

if ($leadForm->mode !== $leadForm::VIEW_MODE || $is_manager) {
    $js = <<<JS

    $(document).on('keyup','.ext-mark-up', function (event) {
        let key = event.keyCode ? event.keyCode : event.which;
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
    
    $('#btn-declined-quotes').on('click', function() {
        var quotes = Array();
        $('.quotes-uid:checked').each(function(idx, elm){
            quotes.push($(elm).val());
        });
        if (quotes.length == 0) {
            createNotify('Warning', 'Check necessary quotes!', 'warning');
            return false;
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
                console.error('Error: ' + error);
            }
        });
    });
    
    $(document).on('click','.quote_details__btn', function (e) {
        e.preventDefault();
        let modal = $('#flight-details__modal');
        $('#flight-details__modal-label').html($(this).data('title'));
        let target = $($(this).data('target')).html();
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
}
?>

<?php
/*$this->registerJs(
    '

        $(document).on("click","#btn-call-expert-form", function() {
            $("#div-call-expert-form").toggle();
            return false;
        });
                

        $("#pjax-lead-call-expert").on("pjax:start", function () {
            //$("#pjax-container").fadeOut("fast");
            $("#btn-submit-call-expert").attr("disabled", true).prop("disabled", true).addClass("disabled");
            $("#btn-submit-call-expert i").attr("class", "fa fa-spinner fa-pulse fa-fw")
            
        });

        $("#pjax-lead-call-expert").on("pjax:end", function () {
            //$("#pjax-container").fadeIn("fast");
            //alert("end");
            
            $("#btn-submit-call-expert").attr("disabled", false).prop("disabled", false).removeClass("disabled");
            $("#btn-submit-call-expert i").attr("class", "fa fa-plus");
            
        });
    '
);*/
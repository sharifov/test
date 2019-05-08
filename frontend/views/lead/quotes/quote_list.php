<?php
/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $leadForm \frontend\models\LeadForm
 * @var $is_manager boolean
 */

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;

?>
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-folder-o"></i> Quotes</h2>
        <ul class="nav navbar-right panel_toolbox">
            <?php if ($leadForm->mode !== $leadForm::VIEW_MODE || $is_manager) : ?>
            <li>
                <?=Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>
            </li>
            <li>
                <?=Html::a('<i class="fa fa-search warning"></i> Quick Search', null, ['class' => '', 'id' => 'quick-search-quotes-btn', 'data-url' => Url::to(['quote/get-online-quotes', 'leadId' => $leadForm->getLead()->id])])?>
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
        <?php Pjax::begin(['id' => 'quotes_list', 'timeout' => 10000]); ?>
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
        <?php Pjax::end() ?>
    </div>
</div>


<?php
$extraPriceUrl = \yii\helpers\Url::to(['quote/extra-price']);
$declineUrl = \yii\helpers\Url::to(['quote/decline']);
$statusLogUrl = \yii\helpers\Url::to(['quote/status-log']);
$previewEmailUrl = \yii\helpers\Url::to(['quote/preview-send-quotes']);
$leadId = $leadForm->getLead()->id;?>

<?php
if ($leadForm->mode !== $leadForm::VIEW_MODE || $is_manager) {
    $js = <<<JS

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
    
    
     /***  Add/Clone quote  ***/
    $(document).on('click','.add-clone-alt-quote', function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var uid = $(this).data('uid');
        var editBlock = $('#create-quote');
        if (uid != 0) {
            editBlock.find('.modal-title').html('Clone quote #' + uid);
        } else {
             editBlock.find('.modal-title').html('Add quote');
        }
        editBlock.find('.modal-body').html('');
        editBlock.find('.modal-body').load(url, function( response, status, xhr ) {
            $('#cancel-alt-quote').attr('data-type', 'direct');
            editBlock.modal({
              backdrop: 'static',
              show: true
            });
        });
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
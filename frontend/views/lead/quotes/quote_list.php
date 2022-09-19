<?php

/**
 * @var $this \yii\web\View
 * @var $lead \common\models\Lead
 * @var $dataProvider \yii\data\ActiveDataProvider
 * @var $leadForm \frontend\models\LeadForm
 * @var $is_manager boolean
 */

use modules\featureFlag\FFlag;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use modules\quoteAward\src\dictionary\AwardProgramDictionary;
use modules\quoteAward\src\forms\AwardQuoteForm;
use src\auth\Auth;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\services\quote\addQuote\guard\FlightQuoteGuard;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use src\helpers\lead\LeadUrlHelper;

$leadAbacDto = new LeadAbacDto($lead, Auth::id());

$addAutoQuoteBtn = '';
$leadAbacDto = new LeadAbacDto($lead, Auth::id());
/** @abac $leadAbacDto, LeadAbacObject::ACT_PRICE_LINK_RESEARCH, LeadAbacObject::ACTION_ACCESS, Access to edit price research links  */
$canAccessPriceResearchLinks = \Yii::$app->abac->can(
    $leadAbacDto,
    LeadAbacObject::ACT_PRICE_LINK_RESEARCH,
    LeadAbacObject::ACTION_ACCESS
);
/** @fflag FFlag::FF_KEY_AWARD_ENABLE, Award Enable */
$enableAward = Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_AWARD_ENABLE);
if ($canAccessPriceResearchLinks) {
    try {
        $priceResearchLinks = SettingHelper::getPriceResearchLinksNamesArray($lead);
    } catch (\RuntimeException | \DomainException $e) {
        Yii::warning(AppHelper::throwableFormatter($e), 'SettingHelper::getPriceResearchLinksNamesArray:exception');
        $priceResearchLinks = [];
    }
} else {
    $priceResearchLinks = [];
}

if (FlightQuoteGuard::canAutoSelectQuotes(Auth::user(), $lead)) {
    $addAutoQuoteBtn = Html::a('<i class="fa fa-plus green"></i> Auto add Quotes', null, ['class' => 'auto_add_quotes_btn', 'data-lead-id' => $lead->id, 'title' => 'Auto-select best options', 'data-toggle' => 'tooltip']);
    $addAutoQuoteUrl = Url::toRoute('/quote/auto-add-quotes');
    $js = <<<JS
    window.leadQuoteAutoAddAjax = false;
    $(document).on('click', '.auto_add_quotes_btn', function (e) {
        e.preventDefault();
        let btn = $(this);
        let btnIcon = $('.fa', btn);
        let iconLoading = $('<i class="fa fa-spin fa-spinner yellow"></i>'); 
        let gds = btn.data('gds');
        let leadId = btn.data('lead-id');
        
        if (leadQuoteAutoAddAjax === false) {
            leadQuoteAutoAddAjax = true;
            btn.attr('data-ajax-send', 1);
            $.ajax({
                url: '$addAutoQuoteUrl',
                type: 'post',
                data: {leadId: leadId, gds: gds},
                dataType: 'json',
                beforeSend: function () {
                    btn.find('i').replaceWith(iconLoading);
                    $('.search-results__wrapper').addClass('loading');
                },
                success: function (response) {
                    if (response.error) {
                        createNotify('Error', response.message, 'error');
                    } else {
                        $.pjax.reload({container: '#quotes_list', async: false});
                        $('.popover-class[data-toggle="popover"]').popover({ sanitize: false });
                        createNotify('Success', response.message, 'success');
                    }
                },
                error: function (xhr) {
                    createNotify('Error', xhr.responseText, 'error');
                },
                complete: function () {
                    btn.find('i').replaceWith(btnIcon)
                    $('.search-results__wrapper').removeClass('loading');
                    leadQuoteAutoAddAjax = false;
                }
            })
        }
    });
JS;
    $this->registerJs($js);
}
?>
<style>
.select2-container--krajee {
    display: block;
    z-index: 9999;
}
</style>

<?php Pjax::begin(['id' => 'quotes_list', 'timeout' => 10000]); ?>
<?php
    $countQuotes = $dataProvider->count;
?>
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-folder-o"></i> Flight Quotes <?= $countQuotes ? '<sup>(' . $countQuotes . ')</sup>' : '' ?></h2>
        <ul class="nav navbar-right panel_toolbox">
            <?php if ($leadForm->mode !== $leadForm::VIEW_MODE || $is_manager) : ?>
                <?php if ($lead->leadFlightSegmentsCount) :?>
                <li>
                    <?= $addAutoQuoteBtn ?>
                </li>
                    <?php /** @abac new $leadAbacDto, LeadAbacObject::OBJ_LEAD_QUOTE_SEARCH, LeadAbacObject::ACTION_ACCESS_QUOTE_SEARCH, Access Quote Search*/?>
                    <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_LEAD_QUOTE_SEARCH, LeadAbacObject::ACTION_ACCESS_QUOTE_SEARCH)) : ?>
                        <li>
                            <?=Html::a('<i class="fa fa-search warning"></i> Quote Search', null, ['class' => '', 'id' => 'search-quotes-btn', 'data-url' => Url::to(['quote/ajax-search-quotes', 'leadId' => $leadForm->getLead()->id])])?>
                        </li>
                    <?php endif; ?>

                    <?php /** @abac new $leadAbacDto, LeadAbacObject::OBJ_LEAD_SMART_SEARCH, LeadAbacObject::ACTION_ACCESS_SMART_SEARCH, Access Smart Search*/?>
                    <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_LEAD_SMART_SEARCH, LeadAbacObject::ACTION_ACCESS_SMART_SEARCH)) :
                        $deepLink = $lead->getDeepLink([]);
                        $deepLinkInfo = LeadUrlHelper::checkDeepLink($deepLink);
                        ?>
                        <li>
                            <?=Html::a('<i class="fas fa-glasses ' . ($deepLinkInfo['error'] ? 'red' : 'blue') . '"></i> Smart Search', $deepLink, [
                                'id' => 'smart-search-btn',
                                'class' => '',
                                'target' => '_blank',
                                'onclick' => 'return ' . ($deepLinkInfo['error'] ? 'false' : 'true') . ';',
                                'data-pjax' => 0,
                                //'onclick' => 'return false;',
                                'title' => $deepLinkInfo['message']])
                            ?>
                        </li>
                    <?php endif; ?>

                <?php else : ?>
                <li>
                  <span class="badge badge-warning"><i class="fa fa-warning"></i> Warning: Flight Segments is empty!</span>
                </li>
                <?php endif; ?>
                <?php if ($canAccessPriceResearchLinks) :?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" title="Market Price Research Tool">
                        <i class="fa fa-link text-info"></i> Price Research
                    </a>
                    <div class="dropdown-menu" role="menu">
                        <?php if ($priceResearchLinks) : ?>
                            <?php foreach ($priceResearchLinks as $key => $priceResearchName) : ?>
                                <?= Html::a('<i class="fa fa-link"></i> "' . Html::encode($priceResearchName) . '"', [
                                    'quote-price-research/open-price-research-link',
                                    'leadId'               => $lead->id,
                                    'researchLinkKey'       => $key
                                ], [
                                    'id' => 'price-research-link-' . $key,
                                    'class' => 'dropdown-item price-research-link',
                                    'target' => '_blank',
                                    'data-pjax' => 0
                                ]) ?>
                            <?php endforeach ?>
                            <div class="dropdown-divider"></div>
                            <?= Html::a(
                                'Open All Links',
                                null,
                                [
                                    'id' => 'btn-open-all-price-research-links',
                                    'class' => 'dropdown-item',
                                    'data-pjax' => 0
                                    ]
                            ) ?>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endif; ?>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <div class="dropdown-menu" role="menu">
                    <?php if ($lead->leadFlightSegmentsCount) :?>
                        <?php /** @abac new $leadAbacDto, LeadAbacObject::OBJ_LEAD_QUICK_SEARCH, LeadAbacObject::ACTION_ACCESS_QUICK_SEARCH, Access Quick Search*/?>
                        <?php if (Yii::$app->abac->can($leadAbacDto, LeadAbacObject::OBJ_LEAD_QUICK_SEARCH, LeadAbacObject::ACTION_ACCESS_QUICK_SEARCH)) : ?>
                            <?=Html::a('<i class="fa fa-search info"></i> Quick Search', null, ['class' => 'dropdown-item', 'id' => 'quick-search-quotes-btn', 'data-url' => Url::to(['quote/get-online-quotes', 'leadId' => $leadForm->getLead()->id])])?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if (!$lead->client->isExcluded()) : ?>
                        <?= Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote dropdown-item', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>
                    <?php endif; ?>
                    <?php
                    if ($enableAward) : ?>
                        <?php if (!$lead->client->isExcluded()) : ?>
                            <?= Html::a('<i class="fa fa-plus-circle success"></i> Add Award Quote', null, ['class' => 'add-clone-alt-quote dropdown-item', 'data-uid' => 0, 'data-url' => Url::to(['quote-award/create', 'leadId' => $leadForm->getLead()->id])]) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?= Html::a('<i class="fa fa-clone success"></i> Clone Quote', null, [
                        'class' => 'clone-quote-by-uid dropdown-item',
                        'title' => 'Clone Quote by UID'
                    ]) ?>
                    <?= Html::a('<i class="fa fa-remove text-danger"></i> Decline Quotes', null, [
                        'class' => 'dropdown-item text-danger',
                        'id' => 'btn-declined-quotes',
                    ]) ?>
                </div>
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
                'totalCount' => $dataProvider->count
            ],
            'itemOptions' => [
                //'class' => 'item',
                'tag' => false,
            ],
        ]);?>
    </div>

    <?= $this->render('_quote_clone_by_id', ['lead' => $leadForm->getLead()])?>
</div>
<?php Pjax::end() ?>


<?php
$extraPriceUrl = \yii\helpers\Url::to(['quote/extra-price']);
$declineUrl = \yii\helpers\Url::to(['quote/decline']);
$statusLogUrl = \yii\helpers\Url::to(['quote/status-log']);
$previewEmailUrl = \yii\helpers\Url::to(['quote/preview-send-quotes']);
$leadId = $leadForm->getLead()->id;?>

<?php
// Email Capture
$js = <<<JS

$(document).on('click','.btn-capture', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-df');
        let title = $(this).attr('title');
        $('#modal-df-label').html(title);     
        modal.find('.modal-body').html('');
        $('#preloader').removeClass('hidden');        
        $.ajax({
            url: url,
            success: function(response){              
                let content = '<textarea rows="2" id="capture-url" readonly="readonly" style="width: 100%">' + response + '</textarea><br><br><div><button class="btn btn-primary btn-clipboard" data-clipboard-target="#capture-url"><i class="fas fa-copy"></i> Copy to clipboard</button></div>';
                modal.find('.modal-body').html(content);
                modal.modal('show'); 
            }
        });
    }); 
JS;
$this->registerJs($js);

// Menu details

$js = <<<JS
$(document).on('click','.btn-quote-details', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        //var modal = $('#modal-info-d');
        $('#modal-lg-label').html($(this).data('title'));
        //modal.find('.modal-header h2').text($(this).data('title'));
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
        let modal = $('#modal-df');
        $('#modal-df-label').html('Quote Status Log');
        modal.find('.modal-body').html('');
        let id = $(this).attr('data-id');
        modal.find('.modal-body').load('$statusLogUrl?quoteId='+id, function( response, status, xhr ) {
            $('#preloader').addClass('hidden');
            modal.modal('show');
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
    $leadId = $leadForm->getLead()->id;
    $js = <<<JS


// todo delete

//    $(document).on('keyup','.ext-mark-up', function (event) {
//        let key = event.keyCode ? event.keyCode : event.which;
//        validatePriceField($(this), key);
//    });
//
//    $(document).on('change','.ext-mark-up', function (event) {
//        if ($(this).val().length == 0) {
//            $(this).val(0);
//        }
//        var element = $(this);
//        $.ajax({
//            type: 'post',
//            url: '$extraPriceUrl',
//            data: {'quote_uid': $(this).data('quote-uid'), 'value': $(this).val(), 'pax_type': $(this).data('pax-type')},
//            success: function (data) {
//                if (!jQuery.isEmptyObject(data)) {
//                    var sell = element.parent().parent().find('.sellingPrice-'+data.uid),
//                            totalSell = $('.total-sellingPrice-'+data.uid),
//                            totalMarkup = $('.total-markup-'+data.uid);
//
//                        sell.text(data.actual.sellingPrice);
//                        totalSell.text(data.total.sellingPrice);
//                        totalMarkup.text(data.total.markup);
//
//                        $('#isChangedMarkup-'+data.uid).removeClass('hidden');
//                    }
//            },
//            error: function (error) {
//            console.log('Error: ' + error);
//            }
//        });
//    });
    
    $(document).on('click', '#btn-declined-quotes', function() {
        var quotes = Array();
        $('.quotes-uid:checked').each(function(idx, elm){
            quotes.push($(elm).val());
        });
        if (quotes.length == 0) {
            createNotify('Warning', 'Check necessary quotes!', 'warning');
            return false;
        }
        var dataPost = {quotes: quotes, leadId: $leadId};
        $('#preloader').removeClass('hidden');
        $.ajax({
            type: 'post',
            url: '$declineUrl',
            data: dataPost,
            success: function (data) {
                $('#preloader').addClass('hidden');
                $.pjax.reload({container: '#quotes_list', async: false});
                $('#c_quotes').val('');
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
    
    $(document).on('click', '#btn-open-all-price-research-links', function () {
        $('.price-research-link').each(function( index ) {
            $(this)[0].click();
        });
    });
   

JS;
    $this->registerJs($js);
}
?>

<?php
$js = <<<JS
    $(document).on('pjax:end', function() {
        $('[data-toggle="tooltip"]').tooltip({html:true});
        $('.popover-class[data-toggle="popover"]').popover({sanitize: false, html: true});
    });
    $('[data-toggle="tooltip"]').tooltip({html:true});
    $('.popover-class[data-toggle="popover"]').popover({sanitize: false, html: true});
JS;
$this->registerJs($js);

if ($enableAward) {
    $this->render('/quote-award/parts/_js_award', ['lead' => $lead]);
}
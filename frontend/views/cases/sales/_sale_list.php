<?php

use frontend\helpers\JsonHelper;
use src\auth\Auth;
use common\models\CreditCard;
use common\models\search\CreditCardSearch;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CaseSaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $caseModel \src\entities\cases\Cases */
/* @var $saleSearchModel common\models\search\SaleSearch */
/** @var $saleDataProvider yii\data\ArrayDataProvider
 * @var $disableMasking bool
 */

$user = Yii::$app->user->identity;

$userCanRefresh = Auth::can('/cases/ajax-refresh-sale-info');
$userCanCheckFareRules = Auth::can('/cases/ajax-refresh-sale-info');
$userCanDeleteSaleData = Auth::can('/sale/delete-ajax');
?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-list"></i> Sale List</h2>
        <ul class="nav navbar-right panel_toolbox">
            <?php if (Auth::can('cases/update', ['case' => $caseModel])) : ?>
                <li>
                    <?=Html::a('<i class="fa fa-search warning"></i> Search Sales', null, ['class' => 'modal', 'id' => 'search-sale-btn', 'title' => 'Search Sales for Case'])?>
                </li>
                <?php /*<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                    <ul class="dropdown-menu" role="menu">
                        <li> <?= Html::a('<i class="fa fa-remove"></i> Decline Quotes', null, [
                                //'class' => 'btn btn-primary btn-sm',
                                'id' => 'btn-declined-quotes',
                            ]) ?>
                        </li>
                    </ul>
                </li>*/?>
            <?php endif; ?>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block;">
        <div class="case-sale-index">

            <table class="table table-bordered table-striped" style="padding: 10px; color: #0d3349;"><tr>
                    <td style="width: 11%">Sale ID</td>
                    <td style="width: 15%">Book Id</td>
                    <td style="width: 15%">PNR</td>
                    <td style="width: 10%">Pax</td>
                    <td>Sale Created Date</td>
                    <td>Added Date</td>
                    <?php if ($caseModel->isProcessing()) : ?>
                        <td>Update to B/O</td>
                    <?php endif; ?>
                    <?php if ($userCanRefresh) : ?>
                        <td>Refresh Data From B/O</td>
                    <?php endif; ?>
                    <?php if ($userCanCheckFareRules) : ?>
                        <td>Check Fare rules</td>
                    <?php endif; ?>
                    <?php if ($userCanDeleteSaleData) : ?>
                        <td>Remove Sale</td>
                    <?php endif; ?>
                    <td>Cancel Sale</td>
                    <td>Resend Tickets</td>
                </tr>
            </table>

            <?php Pjax::begin(['id' => 'pjax-sale-list']); ?>

            <?php

            $itemColls = [];
            if ($items = $dataProvider->getModels()) {
                foreach ($items as $itemKey => $item) {
                    $dataSale = JsonHelper::decode($item->css_sale_data_updated);
                    $saleStatus = '';
                    if (is_array($dataSale)) {
                        $saleStatus = isset($dataSale['saleStatus']) ? mb_strtolower($dataSale['saleStatus']) : '';
                    }

                    $label = '<table class="table table-bordered table-striped" style="margin: 0; color: #0d3349; font-size: 14px"><tr>
                        <td style="width: 10%">Id: ' . Html::encode($item->css_sale_id) . '</td>
                        <td style="width: 15%">' . Html::encode($item->css_sale_book_id) . '</td>
                        <td style="width: 15%">' . Html::encode($item->css_sale_pnr) . '</td>
                        <td style="width: 10%">' . Html::encode($item->css_sale_pax) . '</td>
                        <td>' . Yii::$app->formatter->asDatetime($item->css_sale_created_dt) . '</td>
                        <td>' . Yii::$app->formatter->asDatetime($item->css_created_dt) . '</td>';

                    if ($caseModel->isProcessing()) {
                        $label .= '<td>';

                        $label .= Html::button('<i class="fa fa-upload"></i> Update', [
                            'class' => 'update-to-bo btn ' . ($item->css_need_sync_bo ? 'btn-success' : 'btn-default'),
                            'disabled' => !$item->css_need_sync_bo ? true : false,
                            'id' => 'update-to-bo-' . $item->css_sale_id,
                            'data-case-id' => $item->css_cs_id,
                            'data-case-sale-id' => $item->css_sale_id,
                            'title' => 'Update data to B/O'
                            ]);

                        $label .= '</td>';
                    }
                    if ($userCanRefresh) {
                        $label .= '<td>' . Html::button('<i class="fa fa-refresh"></i> Refresh', [
                                'class' => 'refresh-from-bo btn btn-info refresh-fr-0',
                                'data-case-id' => $item->css_cs_id,
                                'data-case-sale-id' => $item->css_sale_id,
                                'check-fare-rules' => 0,
                                'title' => 'Refresh from B/O'
                        ]) . '</td>';
                    }
                    if ($userCanCheckFareRules) {
                        $label .= '<td>' . Html::button('<i class="fa fa-refresh"></i> Check Fare rules', [
                                'class' => 'refresh-from-bo btn btn-info refresh-fr-1',
                                'data-case-id' => $item->css_cs_id,
                                'data-case-sale-id' => $item->css_sale_id,
                                'check-fare-rules' => 1,
                                'title' => 'Check Fare rules',
                        ]) . '</td>';
                    }
                    if ($userCanDeleteSaleData && Auth::can('cases/update', ['case' => $caseModel])) {
                        $label .= '<td>' . Html::button('<i class="fa fa-warning"></i> Remove', [
                            'class' => 'remove-sale btn btn-danger',
                            'data-case-id' => $item->css_cs_id,
                            'data-case-sale-id' => $item->css_sale_id,
                            'title' => 'Remove Sale',
                        ]) . '</td>';
                    }

                    $label .= '<td>' . Html::button('<i class="fa fa-minus-circle"></i> Cancel', [
                        'class' => 'cancel-sale btn btn-warning',
                        'disabled' => $saleStatus !== 'pending' && $saleStatus !== 'processing',
                        'data-case-id' => $item->css_cs_id,
                        'data-case-sale-id' => $item->css_sale_id,
                        'title' => 'Cancel Sale',
                    ]) . '</td>';

                    $label .= '<td>' . Html::button('<i class="fa fa-share fa-rotate-0"></i> Resend', [
                        'class' => 'resend-tickets btn btn-success',
                        'disabled' => $saleStatus !== 'close',
                        'data-case-id' => $item->css_cs_id,
                        'data-case-sale-id' => $item->css_sale_id,
                        'title' => 'Resend Tickets Sale',
                    ]) . '</td>';

                    $label .= '</tr></table>';

                    $content = '';

                    if (is_array($dataSale)) {
                        $dataProviderCc = new ActiveDataProvider([
                            'query' => CreditCard::find()->innerJoin('sale_credit_card', 'scc_cc_id=cc_id')->where(['scc_sale_id' => $item->css_sale_id]),
                        ]);

                        $content = $this->render('/sale/view', [
                            'data' => $dataSale,
                            'csId' => $caseModel->cs_id,
                            'caseSaleModel' => $item,
                            'itemKey' => $itemKey,
                            'dataProviderCc' => $dataProviderCc,
                            'caseModel' => $caseModel,
                            'additionalData' => [],
                            'disableMasking' => $disableMasking
                        ]);
                    }

                    $itemColls[] = [
                        'label' => $label,
                        'content' => $content,
                        'contentOptions' => ['class' => $itemKey ? '' : 'show'],
                        'pjax' => true
                        //'options' => [...],
                        //'footer' => 'Footer' // the footer label in list-group
                    ];
                }
            }

            echo \yii\bootstrap\Collapse::widget([
                'encodeLabels' => false,
                'items' => $itemColls,
            ]);

            ?>


            <?php /*= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],

                    //'css_cs_id',
                    'css_sale_id',
                    'css_sale_book_id',
                    'css_sale_pnr',
                    'css_sale_pax',
                    'css_sale_created_dt',
                    //'css_sale_data',
                    // 'css_created_user_id',
                    // 'css_updated_user_id',
                    'css_created_dt',
                    'css_updated_dt',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);*/ ?>

            <?php Pjax::end(); ?>

        </div>
    </div>
</div>



<?php
/*yii\bootstrap4\Modal::begin([
    'headerOptions' => ['id' => 'modalSaleSearchHeader'],
    'id' => 'modalSaleSearch',
    'size' => 'modal-lg',
    'clientOptions' => ['backdrop' => 'static']//, 'keyboard' => FALSE]
]);
?>
    <?= $this->render('_sale_search', [
        'searchModel' => $saleSearchModel,
        'dataProvider' => $saleDataProvider,
        'caseModel' => $caseModel,
        'isAdmin'       => $isAdmin
    ])
    ?>
<?php
yii\bootstrap4\Modal::end();
*/
$jsCode = <<<JS
    $(document).on('click', '#search-sale-btn', function(){
        $('#search-sale-panel').toggle();
        //$('#modalSaleSearch').modal('show').find('#modalLeadSearchContent').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
        //$('#modalSaleSearchHeader').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
       return false;
    });

JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);
$urlRefresh = Url::to(['/cases/ajax-refresh-sale-info']);
$urlSalePrepareResendTickets = Url::to(['/sale/prepare-resend-tickets']);
$urlSalePrepareCancelSale = Url::to(['/sale/prepare-cancel-sale']);

$js = <<<JS

let userCanRefresh = "$userCanRefresh";
document.activateButtonSync = function(data) {
    if (data.output === '' && data.message === '' && data.sync) {
        $('#update-to-bo-'+data.caseSaleId).removeAttr('disabled').removeClass('btn-default').addClass('btn-success');
    }else {
        $('#update-to-bo-'+data.caseSaleId).attr('disabled', true).removeClass('btn-success').addClass('btn-default');
    }
    
    if (data.success_message !== '') {
        createNotifyByObject({
            title: data.sync ? 'Updated' : 'Warning',
            type: data.sync ? 'success' : 'warning',
            text: data.success_message,
            hide: true,
            delay: data.sync ? 2000 : 4000,
        });
    }
};

( function () {
    $('.cssSaleData_passengers_birth_date').off('editableSuccess');
})();


$(document).on('click', '.refresh-from-bo', function (e) {
    e.preventDefault();
    e.stopPropagation();  
    
    let obj = $(this),
        caseId = obj.attr('data-case-id'),
        caseSaleId = obj.attr('data-case-sale-id'),
        checkFareRules = obj.attr('check-fare-rules'),
        loader = $('#preloader');
        
    if (typeof checkFareRules === typeof undefined) {
        checkFareRules = 0;    
    }
    
    $.ajax({
        url: "$urlRefresh/" + caseId + '/' + caseSaleId,
        type: 'post',
        data : {check_fare_rules: checkFareRules},
        dataType: "json",    
        beforeSend: function () {
            obj.attr('disabled', true).find('i').toggleClass('fa-spin');
            $(obj).closest('.panel').find('.error-dump').html();
            loader.removeClass('d-none');
        },
        success: function (data) {
            if (data.error) {
               createNotifyByObject({
                    title: "Error",
                    type: "error",
                    text: data.message,
                    hide: true
                }); 
            } else {
                createNotifyByObject({
                    title: "Success",
                    type: "success",
                    text: data.message,
                    hide: true
                }); 
                $.pjax.reload({container: '#pjax-sale-list',push: false, replace: false, 'scrollTo': false, timeout: 1000, async: false,});
                $.pjax.reload({container: '#pjax-case-orders', push: false, replace: false, 'scrollTo': false, timeout: 1000, async: false,});
                
                let jsLocaleClientEl = $('.js_locale_client');
                if (data.locale && jsLocaleClientEl.length) {
                    jsLocaleClientEl.text(data.locale);      
                }
                let jsCountryClientEl = $('.js_marketing_country');
                if (data.marketing_country && jsCountryClientEl.length) {
                    jsCountryClientEl.text(data.marketing_country);      
                }
            }
        },
        error: function (text) {
            createNotifyByObject({
                title: "Error",
                type: "error",
                text: "Internal Server Error. Try again letter.",
                hide: true
            });
        },
        complete: function () {
            obj.removeAttr('disabled').find('i').toggleClass('fa-spin');
            $(obj).closest('.panel').find('.error-dump').html();
            loader.addClass('d-none');
        }
    });
});

$(document).on('click', '.sale-ticket-generate-email-btn', function (e) {
    e.preventDefault();
    var btn = $(this);
    var url = btn.attr('href');
    var creditCardExist = btn.attr('data-credit-card-exist');
    
    if (creditCardExist == 0 && !confirm('Use same CC?')) {
        return false;
    }
    
    btn.attr('disabled', true).find('i').addClass('fa-spin').removeClass('fa-envelope').addClass('fa-refresh');
    $.get(url, function(data) {
        if (data.error) {
            createNotify('Error', data.message, 'error');
        } else {
            createNotify('Success', data.message, 'success');
        }
        btn.find('i').removeClass('fa-spin').removeClass('fa-refresh').addClass('fa-envelope');
    });
});

$(document).on('click', '.resend-tickets', function(e){  
    e.preventDefault();
    
    let btn = $(this);
    let caseId = btn.data('case-id');
    let caseSaleId = btn.data('case-sale-id');
    let url = "$urlSalePrepareResendTickets?caseId=" + caseId + '&caseSaleId=' + caseSaleId;
    let modal = $('#modal-md');
    let btnClass = btn.find('i').attr('class');
      
    btn.addClass('disabled').find('i').attr('class', 'fas fa-spinner fa-spin');
    modal.find('.modal-body').html('');
    modal.find('.modal-title').html('Resend Tickets');
    
    modal.find('.modal-body').load(url, function( response, status, xhr ) {
        modal.modal({
          backdrop: 'static',
          show: true
        });
        btn.removeClass('disabled').find('i').attr('class', btnClass);
    });
});

$(document).on('click', '.cancel-sale', function (e) {
    e.preventDefault();
    
    let btn = $(this);
    let caseId = btn.data('case-id');
    let caseSaleId = btn.data('case-sale-id');
    let url = "$urlSalePrepareCancelSale?caseId=" + caseId + '&caseSaleId=' + caseSaleId;
    let modal = $('#modal-sm');
    let btnClass = btn.find('i').attr('class');
    
    btn.addClass('disabled').find('i').attr('class', 'fas fa-spinner fa-spin');
    modal.find('.modal-body').html('');
    modal.find('.modal-title').html('Cancel Sale');
    
    modal.find('.modal-body').load(url, function( response, status, xhr ) {
        modal.modal({
          backdrop: 'static',
          show: true
        });
        btn.removeClass('disabled').find('i').attr('class', btnClass);
    });
});
JS;
$this->registerJs($js);

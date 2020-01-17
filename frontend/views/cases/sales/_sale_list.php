<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CaseSaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $caseModel \sales\entities\cases\Cases */
/* @var $saleSearchModel common\models\search\SaleSearch */
/* @var $saleDataProvider yii\data\ArrayDataProvider */

$user = Yii::$app->user->identity;
?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-list"></i> Sale List</h2>
        <ul class="nav navbar-right panel_toolbox">
            <?php if($caseModel->isProcessing()):?>
                <li>
                    <?=Html::a('<i class="fa fa-search warning"></i> Search Sales', null, ['class' => 'modal', 'id' => 'search-sale-btn', 'title' => 'Search Sales for Case'])?>
                </li>
                <?/*<li class="dropdown">
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
                    <?php if ($caseModel->isProcessing()): ?>
                        <td>Update to B/O</td>
                    <?php endif; ?>

                    <?php if ($user->isAdmin() || $user->isSuperAdmin()): ?>
                        <td>Refresh Data From B/O</td>
                    <?php endif; ?>
                </tr>
            </table>

            <?php Pjax::begin(['id' => 'pjax-sale-list']); ?>

            <?php

            $itemColls = [];
            if($items = $dataProvider->getModels()) {
                foreach ($items as $itemKey => $item) {

                    $label = '<table class="table table-bordered table-striped" style="margin: 0; color: #0d3349; font-size: 14px"><tr>
                        <td style="width: 10%">Id: '.Html::encode($item->css_sale_id).'</td>
                        <td style="width: 15%">'.Html::encode($item->css_sale_book_id).'</td>
                        <td style="width: 15%">'.Html::encode($item->css_sale_pnr).'</td>
                        <td style="width: 10%">'.Html::encode($item->css_sale_pax).'</td>
                        <td>'.Yii::$app->formatter->asDatetime($item->css_sale_created_dt).'</td>
                        <td>'.Yii::$app->formatter->asDatetime($item->css_created_dt).'</td>';

                    if ($caseModel->isProcessing()) {
                        $label .= '<td>' . Html::button('<i class="fa fa-upload"></i> Upload data to B/O', [
							'class' => 'update-to-bo btn ' . ($item->css_need_sync_bo ? 'btn-success' : 'btn-warning'),
							'disabled' => !$item->css_need_sync_bo ? true : false,
							'id' => 'update-to-bo-' . $item->css_sale_id,
                            'data-case-id' => $item->css_cs_id,
                            'data-case-sale-id' => $item->css_sale_id
                            ]) . '</td>';
                    }
					if ($user->isAdmin() || $user->isSuperAdmin()) {
					    $label .= '<td>' . Html::button('<i class="fa fa-refresh"></i> Refresh from B/O', [
					            'class' => 'refresh-from-bo btn btn-info',
								'data-case-id' => $item->css_cs_id,
								'data-case-sale-id' => $item->css_sale_id
                            ]) . '</td>';
                    }
                    $label .= '</tr></table>';

                    $content = '';

                    $dataSale = @json_decode($item->css_sale_data_updated, true);
//                    echo '<pre>';
//                    print_r($dataSale);die;
                    if(is_array($dataSale)) {
                        $content = $this->render('/sale/view', ['data' => $dataSale, 'csId' => $caseModel->cs_id, 'caseSaleModel' => $item]);
                        //echo '******';
                        //\yii\helpers\VarDumper::dump($content); exit;
                    }



                    $itemColls[] = [
                        'label' => $label,
                        'content' => $content,
                        'contentOptions' => ['class' => $itemKey ? '' : 'show']
                        //'options' => [...],
                        //'footer' => 'Footer' // the footer label in list-group
                    ];
                }
            }

            echo \yii\bootstrap\Collapse::widget([
                'encodeLabels' => false,
                'items' => $itemColls
            ]);

            ?>


            <?/*= GridView::widget([
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

$js = <<<JS
document.activateButtonSync = function(data) {
    if (data.output === '' && data.message === '' && data.sync) {
        $('#update-to-bo-'+data.caseSaleId).removeAttr('disabled').removeClass('btn-warning').addClass('btn-success');
    }else {
        $('#update-to-bo-'+data.caseSaleId).attr('disabled', true).removeClass('btn-success').addClass('btn-warning');
    }
    
    if (data.success_message !== '') {
        new PNotify({
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
JS;
$this->registerJs($js);

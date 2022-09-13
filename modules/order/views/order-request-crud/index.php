<?php

use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\orderRequest\OrderRequest;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\order\src\entities\orderRequest\search\OrderRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Order Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Order Request', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-order-request', 'scrollTo' => 0]); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'orr_id',
            [
                'attribute' => 'orr_request_data_json',
                'format' => 'raw',
                'value' => static function (OrderRequest $model, $key, $index) {
                    $resultStr = '-';
                    if ($decodedData = @json_decode($model->orr_request_data_json, true, 512, JSON_THROW_ON_ERROR)) {
                        $decodedData = \common\helpers\LogHelper::replaceSource($decodedData);
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($decodedData)),
                            500,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($decodedData, 10, true);
                        $detailBox = '<div id="detail_' . $model->orr_id . '_' . $index . '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->orr_id . '_' . $index . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
            ],
            [
                'attribute' => 'orr_response_data_json',
                'format' => 'raw',
                'value' => static function (OrderRequest $model) {
                    $resultStr = '-';
                    if ($decodedData = @json_decode($model->orr_response_data_json, true, 512, JSON_THROW_ON_ERROR)) {
                        $decodedData = \common\helpers\LogHelper::replaceSource($decodedData);
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($decodedData)),
                            500,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($decodedData, 10, true);
                        $detailBox = '<div id="detail_' . $model->orr_id . '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' . $model->orr_id . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
            ],
            [
                'attribute' => 'orr_source_type_id',
                'value' => static function (OrderRequest $model) {
                    return $model->getSourceName();
                },
                'filter' => OrderSourceType::LIST
            ],
            [
                'attribute' => 'orr_response_type_id',
                'value' => static function (OrderRequest $model) {
                    return $model->getResponseType();
                },
                'filter' => OrderRequest::RESPONSE_TYPE_LIST
            ],
            [
                'attribute' => 'orr_created_dt',
                'format' => 'byUserDateTime',
                'options' => [
                    'width' => '200px'
                ]
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
yii\bootstrap4\Modal::begin([
    'title' => '',
    'id' => 'modal',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
]);
yii\bootstrap4\Modal::end();

$js = <<<JS
$(document).on('click', '.showDetail', function(){
        
    let id = $(this).data('idt');
    let detailEl = $('#detail_' + id);
    let modalBodyEl = $('#modal .modal-body');
    
    modalBodyEl.html(detailEl.html()); 
    $('#modal-label').html('Detail json (' + id + ')');       
    $('#modal').modal('show');
    return false;
});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);


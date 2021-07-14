<?php

use frontend\helpers\JsonHelper;
use modules\product\src\entities\productType\ProductType;
use sales\model\coupon\entity\couponProduct\CouponProduct;
use yii\grid\ActionColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\coupon\entity\couponProduct\CouponProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Coupon Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="coupon-product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Coupon Product', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-coupon-product']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'cup_coupon_id:coupon',
            [
                'attribute' => 'cup_product_type_id',
                'value' => static function (CouponProduct $model) {
                    return ArrayHelper::getValue($model, 'cupProductType.pt_key', '');
                },
                'format' => 'raw',
                'filter' => ProductType::getList(),
            ],
            [
                'attribute' => 'cup_data_json',
                'format' => 'raw',
                'value' => static function (CouponProduct $model) {
                    $resultStr = '-';
                    if ($decodedData = JsonHelper::decode($model->cup_data_json)) {
                        $truncatedStr = StringHelper::truncate(
                            Html::encode(VarDumper::dumpAsString($decodedData)),
                            300,
                            '...',
                            null,
                            false
                        );

                        $detailData = VarDumper::dumpAsString($decodedData, 10, true);
                        $detailBox = '<div id="detail_' . $model->cup_coupon_id . '-' . $model->cup_product_type_id .
                            '" style="display: none;">' . $detailData . '</div>';
                        $detailBtn = ' <i class="fas fa-eye green showDetail" style="cursor: pointer;" data-idt="' .
                            $model->cup_coupon_id . '-' . $model->cup_product_type_id . '"></i>';

                        $resultStr = $truncatedStr . $detailBox . $detailBtn;
                    }
                    return '<small>' . $resultStr . '</small>';
                },
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
yii\bootstrap4\Modal::begin([
    'title' => 'Detail',
    'id' => 'modal',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
]);
yii\bootstrap4\Modal::end();

$jsCode = <<<JS
    $(document).on('click', '.showDetail', function(){
        
        let logId = $(this).data('idt');
        let detailEl = $('#detail_' + logId);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html(detailEl.html()); 
        $('#modal-label').html('Detail'); 
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);

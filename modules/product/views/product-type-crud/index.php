<?php

use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethodQuery;
use modules\product\src\entities\productTypePaymentMethod\search\ProductTypePaymentMethodSearch;
use modules\product\src\grid\columns\ProductTypeCountPaymentMethodsColumn;
use modules\product\src\grid\columns\ProductTypeDefaultPaymentMethodServiceFeeColumn;
use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\product\src\entities\productType\search\ProductTypeCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'pt_id',
                'options' => ['style' => 'width: 80px']
            ],
            'pt_key',
            'pt_name',

            [
                'attribute' => 'pt_icon_class',
                'value' => static function (ProductType $model) {
                    return $model->pt_icon_class ? Html::tag('i', '', ['class' => $model->pt_icon_class]) . ' "' . Html::encode($model->pt_icon_class) . '"' : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width: 180px']
            ],
            'pt_sort_order',
//            'pt_service_fee_percent',
//            'pt_description:ntext',
            [
                    'label' => 'Count Payment Methods',
                'class' => ProductTypeCountPaymentMethodsColumn::class,
            ],
            [
                    'label' => 'Payment Fee percent',
                'class' => ProductTypeDefaultPaymentMethodServiceFeeColumn::class,
            ],
            [
                'class' => BooleanColumn::class,
                'attribute' => 'pt_enabled',
            ],
//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'pt_created_dt',
//            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pt_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

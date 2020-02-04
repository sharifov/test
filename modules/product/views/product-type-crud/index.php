<?php

use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethodQuery;
use modules\product\src\entities\productTypePaymentMethod\search\ProductTypePaymentMethodSearch;
use sales\yii\grid\BooleanColumn;
use sales\yii\grid\DateTimeColumn;
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
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'pt_id',
            'pt_key',
            'pt_name',
//            'pt_service_fee_percent',
            'pt_description:ntext',
            [
                'value' => static function (ProductType $model) {

                    $count = $model->getProductTypePaymentMethod()->count();

                    $searchClass = (new \ReflectionClass(ProductTypePaymentMethodSearch::class))->getShortName();

                    if ($count) {
                        $link = Html::a($count,
							[
								'/product/product-type-payment-method/index',
								$searchClass . '[ptpm_produt_type_id]' => $model->pt_id
							],
							[
								'data-pjax' => 0,
								'target' => '_blank'
							]);
                    } else {
                        $link = null;
                    }

                    return $link;
                },
                'label' => 'Count Payment Methods',
                'format' => 'raw'
            ],
            [
                'value' => static function (ProductType $model) {
                    $defaultPaymentMethod = ProductTypePaymentMethodQuery::getDefaultPaymentMethodByProductType($model->pt_id);

                    if ($defaultPaymentMethod !== null) {
						$link = HTML::a(
                            $defaultPaymentMethod->ptpm_payment_fee_percent . ' %',
                                    [
                                        '/product/product-type-payment-method/view',
                                        'ptpm_produt_type_id' => $defaultPaymentMethod->ptpm_produt_type_id,
                                        'ptpm_payment_method_id' => $defaultPaymentMethod->ptpm_payment_method_id
                                    ],
                            [
                                'data-pjax' => 0,
                                'target' => '_blank'
                            ]
                        );
                    } else {
						$link = null;
                    }

                    return $link;
                },
                'label' => 'Default Payment Methods Service Fee',
                'format' => 'raw'
            ],
            [
                'class' => BooleanColumn::class,
                'attribute' => 'pt_enabled',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pt_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pt_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php

use common\models\Employee;
use common\models\PaymentMethod;
use modules\product\src\entities\productType\ProductTypeQuery;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethod;
use sales\yii\grid\BooleanColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\product\src\entities\productTypePaymentMethod\search\ProductTypePaymentMethodSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Product Type Payment Methods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-type-payment-method-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Product Type Payment Method', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'ptpm_produt_type_id',
                'value' => static function (ProductTypePaymentMethod $model) {
                    return $model->ptpmProdutType->pt_name;
                },
                'filter' => ProductTypeQuery::getListAll()
            ],
            [
                'attribute' => 'ptpm_payment_method_id',
                'value' => static function (ProductTypePaymentMethod $model) {
                    return $model->ptpmPaymentMethod->pm_name;
                },
                'filter' => PaymentMethod::getList()
            ],
            [
                'attribute' => 'ptpm_payment_fee_percent',
                'value' => static function (ProductTypePaymentMethod $model) {
                    return $model->ptpm_payment_fee_percent . ' %';
                }
            ],
            'ptpm_payment_fee_amount',
			[
				'class' => BooleanColumn::class,
				'attribute' => 'ptpm_enabled',
			],
			[
				'class' => BooleanColumn::class,
				'attribute' => 'ptpm_default',
			],
            [
                'class' => UserColumn::class,
                'attribute' => 'ptpm_created_user_id',
                'relation' => 'ptpmCreatedUser'
            ],
			[
				'class' => UserColumn::class,
				'attribute' => 'ptpm_updated_user_id',
				'relation' => 'ptpmUpdatedUser'
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'ptpm_created_dt',
			],
            [
				'class' => DateTimeColumn::class,
				'attribute' => 'ptpm_updated_dt',
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

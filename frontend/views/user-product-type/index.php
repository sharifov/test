<?php

use common\models\UserProductType;
use modules\product\src\entities\productType\ProductType;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserProductTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Product Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-product-type-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Product Type', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
				'attribute' => 'upt_user_id',
				'value' => static function (UserProductType $model) {
					return '<i class="fa fa-user"></i> ' . Html::encode($model->user->username);
				},
				'format' => 'raw',
				'filter' => \common\models\Employee::getList()
			],
            [
				'attribute' => 'upt_product_type_id',
				'value' => static function (UserProductType $model) {
					return '<i class="fa fa-list"></i> ' . Html::encode($model->productType->pt_name);
				},
				'format' => 'raw',
				'filter' => ProductType::getList()
			],
            'upt_commission_percent',
            'upt_product_enabled:booleanByLabel',
            [
                'attribute' => 'upt_created_dt',
                'value' => static function (UserProductType $model) {
                    return $model->upt_created_dt ?
                        '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->upt_created_dt)) : '-';
                },
                'format' => 'raw',
                'enableSorting' => false
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

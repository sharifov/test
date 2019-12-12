<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\InvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invoice-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Invoice', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'inv_id',
            'inv_gid',
            'inv_uid',
            'inv_order_id',
            'inv_status_id',
            'inv_sum',
            'inv_client_sum',
            'inv_client_currency',
            'inv_currency_rate',
            'inv_description:ntext',
//            'inv_created_user_id',
//            'inv_updated_user_id',
            [
                'attribute' => 'inv_created_user_id',
                'value' => static function(\common\models\Invoice $model){
                    return $model->invCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->invCreatedUser->username) : '-';
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],

            [
                'attribute' => 'inv_updated_user_id',
                'value' => static function(\common\models\Invoice $model){
                    return $model->invUpdatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->invUpdatedUser->username) : '-';
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],

            [
                'attribute' => 'inv_created_dt',
                'value' => static function(\common\models\Invoice $model) {
                    return $model->inv_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->inv_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'inv_updated_dt',
                'value' => static function(\common\models\Invoice $model) {
                    return $model->inv_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->inv_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],

            //'inv_created_dt',
            //'inv_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

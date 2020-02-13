<?php

use modules\invoice\src\grid\columns\InvoiceStatusColumn;
use modules\order\src\grid\columns\OrderColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\invoice\src\entities\invoice\search\InvoiceCrudSearch */
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
            [
                'class' => OrderColumn::class,
                'attribute' => 'inv_order_id',
                'relation' => 'invOrder',
            ],
            [
                'class' => InvoiceStatusColumn::class,
                'attribute' => 'inv_status_id',
            ],
            'inv_sum',
            'inv_client_sum',
            'inv_client_currency',
            'inv_currency_rate',
            'inv_description:ntext',
            [
                'class' => UserColumn::class,
                'attribute' => 'inv_created_user_id',
                'relation' => 'invCreatedUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'inv_updated_user_id',
                'relation' => 'invUpdatedUser',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'inv_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'inv_updated_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>

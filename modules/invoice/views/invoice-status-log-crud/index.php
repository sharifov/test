<?php

use modules\invoice\src\grid\columns\InvoiceColumn;
use modules\invoice\src\grid\columns\InvoiceStatusActionColumn;
use modules\invoice\src\grid\columns\InvoiceStatusColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\DurationColumn;
use sales\yii\grid\UserColumn;
use yii\grid\ActionColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\invoice\src\entities\invoiceStatusLog\search\InvoiceStatusLogCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Invoice Status Logs';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="invoice-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create invoice Status Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'invsl_id',
            [
                'class' => InvoiceColumn::class,
                'attribute' => 'invsl_invoice_id',
                'relation' => 'invoice',
            ],
            [
                'class' => InvoiceStatusColumn::class,
                'attribute' => 'invsl_start_status_id',
            ],
            [
                'class' => InvoiceStatusColumn::class,
                'attribute' => 'invsl_end_status_id',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'invsl_start_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'invsl_end_dt',
            ],
            [
                'class' => DurationColumn::class,
                'attribute' => 'invsl_duration',
                'startAttribute' => 'invsl_start_dt',
            ],
            'invsl_description',
            [
                'class' => InvoiceStatusActionColumn::class,
                'attribute' => 'invsl_action_id'
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'invsl_created_user_id',
                'relation' => 'createdUser',
            ],
            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>

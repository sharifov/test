<?php

use modules\invoice\src\grid\columns\InvoiceColumn;
use modules\invoice\src\grid\columns\InvoiceStatusActionColumn;
use modules\invoice\src\grid\columns\InvoiceStatusColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\DurationColumn;
use common\components\grid\UserSelect2Column;
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
                'class' => UserSelect2Column::class,
                'attribute' => 'invsl_created_user_id',
                'relation' => 'createdUser',
                'placeholder' => 'Select User',
            ],

            ['class' => ActionColumn::class],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>

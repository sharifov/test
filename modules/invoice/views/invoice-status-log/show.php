<?php

use modules\invoice\src\entities\invoiceStatusLog\search\InvoiceStatusLogSearch;
use modules\invoice\src\grid\columns\InvoiceStatusActionColumn;
use modules\invoice\src\grid\columns\InvoiceStatusColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\DurationColumn;
use sales\yii\grid\UserColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel InvoiceStatusLogSearch */

?>

<div class="invoice-status-log">

    <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false, //$searchModel,
        'columns' => [
            [
                'attribute' => 'invsl_id',
                'options' => ['style' => 'width:80px'],
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
                'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'invsl_description',
                'format' => 'ntext',
                'options' => ['style' => 'width:280px'],
            ],
            [
                'class' => InvoiceStatusActionColumn::class,
                'attribute' => 'invsl_action_id'
            ],
            [
                'class' => UserColumn::class,
                'relation' => 'createdUser',
                'attribute' => 'invsl_created_user_id',
            ],
        ],
    ]) ?>

    <?php Pjax::end(); ?>
</div>

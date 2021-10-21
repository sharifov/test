<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\src\entities\flightQuoteTicketRefund\search\FlightQuoteTicketRefundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Ticket Refunds';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-ticket-refund-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote Ticket Refund', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'fqtr_id',
            'fqtr_ticket_number',
            'fqtr_created_dt',
            'fqtr_fqb_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

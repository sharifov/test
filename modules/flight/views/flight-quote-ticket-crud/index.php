<?php

use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use yii\grid\SerialColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightQuoteTicketSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-ticket-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Flight Quote Ticket', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-flight-quote-ticket']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],

            'fqt_pax_id',
            'fqt_fqb_id',
            'fqt_ticket_number',
            ['class' => DateTimeColumn::class, 'attribute' => 'fqt_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'fqt_updated_dt'],


            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

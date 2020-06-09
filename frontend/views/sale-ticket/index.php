<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\saleTicket\entity\search\SaleTicketSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sale Tickets';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-ticket-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sale Ticket', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'st_case_id',
            'st_case_sale_id',
            'st_ticket_number',
            'st_record_locator',
            'st_original_fop',
            //'st_charge_system',
            //'st_penalty_type',
            //'st_penalty_amount',
            //'st_selling',
            //'st_service_fee',
            //'st_recall_commission',
            //'st_markup',
            //'st_upfront_charge',
            //'st_refundable_amount',
            //'st_created_dt',
            //'st_updated_dt',
            //'st_created_user_id',
            //'st_updated_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

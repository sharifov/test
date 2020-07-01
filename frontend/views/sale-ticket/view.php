<?php

use sales\model\saleTicket\entity\SaleTicket;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\saleTicket\entity\SaleTicket */

$this->title = 'Sale Ticket - Case Id :' . $model->st_case_id . '; Case Sale Id: ' . $model->st_case_sale_id;
$this->params['breadcrumbs'][] = ['label' => 'Sale Tickets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sale-ticket-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'st_case_id' => $model->st_case_id, 'st_case_sale_id' => $model->st_case_sale_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'st_case_id' => $model->st_case_id, 'st_case_sale_id' => $model->st_case_sale_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'st_id',
                'stCase:case',
                'stCaseSale:caseSale',
                'st_ticket_number',
                'st_record_locator',
                'st_original_fop',
                'st_charge_system',
                [
                    'attribute' => 'st_penalty_type',
                    'format' => 'raw',
                    'value' => static function(SaleTicket $model) {
                        return $model->getPenaltyTypeName($model->st_penalty_type);
                    },
                ],
                'st_penalty_amount',
                'st_selling',
                'st_service_fee',
                'st_recall_commission',
                'st_markup',
                'st_upfront_charge',
                'st_refundable_amount',
                'st_created_dt:byUserDateTime',
                'st_updated_dt:byUserDateTime',
                'st_created_user_id:userName',
                'st_updated_user_id:userName',
            ],
        ]) ?>

    </div>

</div>

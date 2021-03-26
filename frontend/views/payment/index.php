<?php

use common\models\Payment;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Payment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'pay_id',
            'pay_type_id',
            'pay_method_id',
            [
                'attribute' => 'pay_status_id',
                'value' => static function (Payment $model) {
                    return Payment::getStatusName($model->pay_status_id);
                },
                'filter' => Payment::getStatusList()
            ],
            //'pay_date',
            'pay_code',
            'pay_invoice_id',
            'pay_order_id',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'pay_date'
            ],
            //'pay_amount',
            //'pay_currency',
            //'pay_created_user_id',
            //'pay_updated_user_id',
            //'pay_created_dt',
            //'pay_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

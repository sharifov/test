<?php

use yii\grid\ActionColumn;
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

    <?php Pjax::begin(['scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
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
            'pay_code',
            'pay_invoice_id',
            'pay_order_id',
            'pay_billing_id',
            'pay_date',
            'pay_amount',
            'pay_currency',
            //'pay_created_user_id',
            //'pay_updated_user_id',
            //'pay_created_dt',
            //'pay_updated_dt',

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

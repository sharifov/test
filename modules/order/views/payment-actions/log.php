<?php

use common\models\search\TransactionSearch;
use common\models\Transaction;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel TransactionSearch */

?>

<div class="payment-status-log">

    <?php Pjax::begin(['enablePushState' => false, 'enableReplaceState' => false]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
     //   'filterModel' => $searchModel,
        'columns' => [

            'tr_id',
            'tr_code',
            'tr_invoice_id',
            [
                'attribute' => 'tr_type_id',
                'value' => static function (Transaction $model) {
                    return Transaction::getTypeName($model->tr_type_id);
                },
                'filter' => Transaction::getTypeList()
            ],
            'tr_date',
            'tr_amount',
            'tr_currency',
            //'tr_created_dt',
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>

<?php

use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerSearch;
use modules\order\src\processManager\Status;
use modules\order\src\processManager\Type;
use common\components\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel OrderProcessManagerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders Process Managers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'opm_id',
            [
                'attribute' => 'opm_status',
                'value' => static function (OrderProcessManager $orderProcess) {
                    return Status::LIST[$orderProcess->opm_status] ?? 'undefined';
                },
                'filter' => Status::LIST,
            ],
            [
                'attribute' => 'opm_type',
                'value' => static function (OrderProcessManager $orderProcess) {
                    return Type::LIST[$orderProcess->opm_type] ?? 'undefined';
                },
                'filter' => Type::LIST,
            ],
            ['class' => DateTimeColumn::class, 'attribute' => 'opm_created_dt'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>

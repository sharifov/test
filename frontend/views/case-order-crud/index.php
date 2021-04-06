<?php

use common\components\i18n\Formatter;
use sales\model\caseOrder\entity\CaseOrder;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\caseOrder\entity\search\CaseOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Case Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-case-order']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'co_order_id',
                'value' => static function (CaseOrder $caseOrder) {
                    return (new Formatter())->asOrder($caseOrder->order);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'co_case_id',
                'value' => static function (CaseOrder $caseOrder) {
                    return (new Formatter())->asCase($caseOrder->cases);
                },
                'format' => 'raw'
            ],
            'co_create_dt:byUserDateTime',
            'co_created_user_id:username',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

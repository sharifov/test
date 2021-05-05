<?php

use common\components\grid\DateTimeColumn;
use common\components\i18n\Formatter;
use sales\model\leadOrder\entity\LeadOrder;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\leadOrder\entity\search\LeadOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-order-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Lead Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-lead-order']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'lo_order_id',
                'value' => static function (LeadOrder $caseOrder) {
                    return (new Formatter())->asOrder($caseOrder->order);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'lo_lead_id',
                'value' => static function (LeadOrder $caseOrder) {
                    return (new Formatter())->asLead($caseOrder->lead);
                },
                'format' => 'raw'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'lo_create_dt',
                'format' => 'byUserDateTime'
            ],
            'lo_created_user_id:username',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

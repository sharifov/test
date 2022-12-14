<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserCommissionRulesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Commission Rules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-commission-rules-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create User Commission Rules', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ucr_exp_month',
            'ucr_kpi_percent:percentInteger',
            'ucr_order_profit',
            'ucr_value:percentInteger',
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'ucr_created_user_id',
                'relation' => 'ucrCreatedUser',
                'url' => '/employee/list-ajax',
                'headerOptions' => ['style' => 'width:13%'],
            ],
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'ucr_updated_user_id',
                'relation' => 'ucrUpdatedUser',
                'url' => '/employee/list-ajax',
                'headerOptions' => ['style' => 'width:13%'],
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ucr_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ucr_updated_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

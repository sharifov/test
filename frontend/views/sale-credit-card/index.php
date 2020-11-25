<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SaleCreditCardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sale Credit Cards';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-credit-card-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sale Credit Card', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'scc_sale_id',
            'scc_cc_id',
            //'scc_created_dt',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'scc_created_dt'
            ],
            'scc_created_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

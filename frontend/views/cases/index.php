<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cases-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Cases', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cs_id',
            'cs_subject',
            'cs_description:ntext',
            'cs_category',
            'cs_status',
            //'cs_user_id',
            //'cs_lead_id',
            //'cs_call_id',
            //'cs_depart_id',
            //'cs_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

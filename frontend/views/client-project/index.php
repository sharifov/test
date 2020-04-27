<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ClientProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-project-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Project', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cp_client_id',
            'cp_project_id',
            'cp_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

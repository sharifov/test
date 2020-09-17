<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\call\entity\callCommand\search\CallGatherSwitchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Gather Switches';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-gather-switch-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Gather Switch', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cgs_ccom_id',
            'cgs_step',
            'cgs_case',
            'cgs_exec_ccom_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

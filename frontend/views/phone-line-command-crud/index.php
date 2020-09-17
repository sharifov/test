<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\call\entity\callCommand\search\PhoneLineCommandSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Line Commands';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-command-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Phone Line Command', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'plc_id',
            'plc_line_id',
            'plc_ccom_id',
            'plc_sort_order',
            'plc_created_user_id',
            //'plc_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CaseNoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Notes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-note-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Case Note', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'cn_id',
            'cn_cs_id',
            'cn_user_id',
            'cn_text:ntext',
            'cn_created_dt',
            //'cn_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

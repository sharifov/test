<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\callNote\entity\search\CallNoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Call Notes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-note-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Call Note', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'cn_id',
            'cn_call_id:callLog',
            'cn_note',
            'cn_created_dt:byUserDateTime',
            'cn_updated_dt:byUserDateTime',
            'cn_created_user_id:username',
            //'cn_updated_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

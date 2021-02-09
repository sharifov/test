<?php

use modules\fileStorage\src\grid\columns\FileLogTypeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\fileStorage\src\entity\fileLog\search\FileLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'File Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create File Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-file-log']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'fl_id',
            'fl_fs_id',
            'fl_fsh_id',
            ['class' => FileLogTypeColumn::class],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

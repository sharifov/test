<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\fileStorage\src\entity\fileStorage\search\FileStorageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'File Storages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-storage-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create File Storage', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-file-storage']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

                'fs_id',
            'fs_uid',
            'fs_mime_type',
            'fs_name',
            'fs_title',
            //'fs_path',
            //'fs_size',
            //'fs_private:boolean',
            //'fs_expired_dt',
            //'fs_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\fileStorage\src\entity\fileShare\search\FileShareSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'File Shares';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-share-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create File Share', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-file-share']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'fsh_id',
            'fsh_fs_id',
            'fsh_code',
            ['class' => UserSelect2Column::class, 'relation' => 'createdUser', 'attribute' => 'fsh_created_user_id'],
            ['class' => DateTimeColumn::class, 'attribute' => 'fsh_expired_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'fsh_created_dt'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

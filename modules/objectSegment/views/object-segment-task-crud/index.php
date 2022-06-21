<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentTask;
use modules\taskList\src\entities\taskList\TaskList;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\objectSegment\src\entities\search\ObjectSegmentTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Object Segment Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-segment-task-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Object Segment Task', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'ostl_osl_id',
                'value' => function (ObjectSegmentTask $model) {
                    return $model->ostlOsl->osl_title;
                },
                'filter' => ObjectSegmentList::getListCache(),
            ],
            [
                'attribute' => 'ostl_tl_id',
                'value' => function (ObjectSegmentTask $model) {
                    return $model->ostlTl->tl_title;
                },
                'filter' => TaskList::getListCache(),
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ostl_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ostl_created_user_id',
                'relation' => 'ostlCreatedUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, ObjectSegmentTask $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ostl_osl_id' => $model->ostl_osl_id, 'ostl_tl_id' => $model->ostl_tl_id]);
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

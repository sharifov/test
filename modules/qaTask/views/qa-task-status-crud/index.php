<?php

use modules\qaTask\src\grid\columns\QaTaskStatusColumn;
use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\qaTask\src\entities\qaTaskStatus\search\QaTaskStatusCrudSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Qa Task Statuses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-status-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Qa Task Status', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => QaTaskStatusColumn::class,
                'attribute' => 'ts_id'
            ],
            'ts_name',
            'ts_description',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'ts_enabled',
            ],
            'ts_css_class',
            [
                'class' => UserColumn::class,
                'attribute' => 'ts_created_user_id',
                'relation' => 'createdUser',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'ts_updated_user_id',
                'relation' => 'updatedUser',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ts_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ts_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]) ?>

    <?php Pjax::end(); ?>

</div>

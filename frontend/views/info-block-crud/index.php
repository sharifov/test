<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\InfoBlock;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\InfoBlockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Info Block';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="info-block-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{errors}\n{summary}\n{items}\n{pager}",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'ib_title',
            'ib_key',
            'ib_enabled:boolean',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ib_updated_user_id',
                'relation' => 'updatedUser',
                'placeholder' => 'Select User',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ib_updated_dt'
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, InfoBlock $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'ib_id' => $model->ib_id]);
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>

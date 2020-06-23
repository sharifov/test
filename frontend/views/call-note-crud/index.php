<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
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
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cn_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cn_updated_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cn_created_user_id',
				'relation' => 'cnCreatedUser',
                'format' => 'username',
				'placeholder' => 'Select User'
            ],
			[
				'class' => UserSelect2Column::class,
				'attribute' => 'cn_updated_user_id',
				'relation' => 'cnUpdatedUser',
				'format' => 'username',
				'placeholder' => 'Select User'
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

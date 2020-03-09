<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserOnlineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Onlines';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-online-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Online', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            ['label' => 'User Id',
                'value' => static function($model) {
                    return $model->uo_user_id;
                },
            ],
            [
                'class' => \sales\yii\grid\UserColumn::class,
                'attribute' => 'uo_user_id',
                'relation' => 'uoUser',
            ],
            [
                'class' => \sales\yii\grid\DateTimeColumn::class,
                'attribute' => 'uo_updated_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

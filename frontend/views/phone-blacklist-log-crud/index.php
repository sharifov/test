<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\PhoneBlacklistLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Blacklist Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-blacklist-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Phone Blacklist Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-phone-blacklist-log']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pbll_id',
            'pbll_phone',
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'pbll_created_dt',
            ],
            //'pbll_created_user_id:username',

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'pbll_created_user_id',
                'relation' => 'pbllCreatedUser',
                'placeholder' => 'Select User',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

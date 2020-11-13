<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;
/* @var $this yii\web\View */
/* @var $searchModel common\models\EmailUnsubscribeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Unsubscribes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-unsubscribe-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Email Unsubscribe', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('partial/_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'eu_email:email',
            'eu_project_id',
            'eu_created_user_id',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'eu_created_dt'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php

use src\model\userStatDay\entity\UserStatDay;
use src\model\userStatDay\entity\UserStatDayKey;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\userStatDay\entity\search\UserStatDaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Stat Days';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-stat-day-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Stat Day', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'usd_id',
            [
                'attribute' => 'usd_key',
                'value' => static function (UserStatDay $model) {
                    return UserStatDayKey::getNameById($model->usd_key);
                },
                'filter' => UserStatDayKey::getList()
            ],
            'usd_value',
            [
                'label' => 'User',
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'usd_user_id',
                'relation' => 'user',
                'placeholder' => 'Select User',
                'headerOptions' => ['style' => 'width:10%'],
            ],
            'usd_day',
            'usd_month',
            'usd_year',
            'usd_created_dt:byUserDateTime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

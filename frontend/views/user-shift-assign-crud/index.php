<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\userShiftAssign\UserShiftAssign;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\shiftSchedule\src\entities\userShiftAssign\search\SearchUserShiftAssign */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Shift Assigns';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-shift-assign-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create User Shift Assign', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-user-shift-assign', 'scrollTo' => 0]); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //'usa_user_id:username',
            [
                'label' => 'User',
                'class' => UserSelect2Column::class,
                'relation' => 'user',
                'attribute' => 'usa_user_id'
            ],
            [
                'attribute' => 'usa_sh_id',
                'value' => static function (UserShiftAssign $model) {
                    return $model->shift->sh_name ?? Yii::$app->formatter->nullDisplay;
                },
                'filter' => Shift::getList(null),
                'format' => 'raw',
            ],
            //'usa_created_dt',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'usa_created_dt',
            ],
            //'usa_created_user_id',
            [
                'label' => 'Created User',
                'class' => UserSelect2Column::class,
                'relation' => 'createdUser',
                'attribute' => 'usa_created_user_id'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\shiftSchedule\entity\userShiftAssign\search\SearchUserShiftAssign */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Shift Assigns';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-shift-assign-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create User Shift Assign', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-user-shift-assign']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'usa_user_id:username',
            'usa_ssr_id',
            'usa_created_dt',
            'usa_created_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

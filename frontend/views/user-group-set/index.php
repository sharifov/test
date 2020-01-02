<?php

use common\models\UserGroupSet;
use sales\yii\grid\BooleanColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserGroupSetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Group Set';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="user-group-set-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Group Set', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'ugs_id',
            'ugs_name',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'ugs_enabled',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ugs_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ugs_updated_dt',
            ],
            [
                'class' => UserColumn::class,
                'attribute' => 'ugs_updated_user_id',
                'relation' => 'updatedUser',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\phoneList\entity\search\PhoneListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Phone List', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization Phones from Communication', ['synchronization'], ['class' => 'btn btn-warning', 'data' => [
            'confirm' => 'Are you sure you want synchronization all projects from Communication Server?',
            'method' => 'post',
        ],]) ?>
    </p>

    <?php Pjax::begin(['timeout' => 5000]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pl_id',
            'pl_phone_number',
            'pl_title',
            ['class' => BooleanColumn::class, 'attribute' => 'pl_enabled'],
            ['class' => UserSelect2Column::class, 'attribute' => 'pl_created_user_id', 'relation' => 'createdUser'],
            ['class' => UserSelect2Column::class, 'attribute' => 'pl_updated_user_id', 'relation' => 'updatedUser'],
            ['class' => DateTimeColumn::class, 'attribute' => 'pl_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'pl_updated_dt'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

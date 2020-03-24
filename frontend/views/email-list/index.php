<?php

use sales\model\emailList\entity\search\EmailListSearch;
use sales\yii\grid\BooleanColumn;
use sales\yii\grid\DateTimeColumn;
use sales\yii\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel EmailListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Email List', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'el_id',
            'el_email',
            'el_title',
            ['class' => BooleanColumn::class, 'attribute' => 'el_enabled'],
            ['class' => UserSelect2Column::class, 'attribute' => 'el_created_user_id', 'relation' => 'createdUser'],
            ['class' => UserSelect2Column::class, 'attribute' => 'el_updated_user_id', 'relation' => 'updatedUser'],
            ['class' => DateTimeColumn::class, 'attribute' => 'el_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'el_updated_dt'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

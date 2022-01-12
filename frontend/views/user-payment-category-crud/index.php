<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\user\entity\paymentCategory\search\UserPaymentCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Payment Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-payment-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Payment Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'upc_id',
            'upc_name',
            'upc_description',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'upc_enabled',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'upc_created_user_id',
                'relation' => 'upcCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'upc_updated_user_id',
                'relation' => 'upcUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'upc_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'upc_updated_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

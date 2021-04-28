<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\abac\src\entities\search\AbacPolicySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Abac Policies';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abac-policy-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Abac Policy', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'ap_id',
            'ap_sort_order',
            'ap_rule_type',
            //'ap_subject',
            'ap_subject_json',
            'ap_object',
            //'ap_action',
            'ap_action_json',
            'ap_effect',
            'ap_title',

//            'ap_created_dt',
//            'ap_updated_dt',

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ap_created_user_id',
                'relation' => 'apCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ap_updated_user_id',
                'relation' => 'apUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ap_created_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ap_updated_dt',
            ],

            //'ap_created_user_id',
            //'ap_updated_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
